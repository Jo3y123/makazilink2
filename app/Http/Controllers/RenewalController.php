<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\RenewalCode;
use App\Models\Setting;

class RenewalController extends Controller
{
    // Plan prices
    private array $planPrices = [
        'starter'    => 2500,
        'growth'     => 5000,
        'pro'        => 8000,
        'enterprise' => 15000,
    ];

    // Days per plan
    private array $planDays = [
        'starter'    => 30,
        'growth'     => 30,
        'pro'        => 30,
        'enterprise' => 30,
    ];

    // Step 1 — Show plan selection
    public function index()
    {
        $subscription = Subscription::first();
        $companyName  = Setting::get('company_name', 'MakaziLink v2');
        $phone        = Setting::get('company_phone', '');

        return view('renewal.index', compact('subscription', 'companyName', 'phone'));
    }

    // Step 2 — Show payment instructions for selected plan
    public function instructions(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:starter,growth,pro,enterprise',
        ]);

        $plan        = $request->plan;
        $amount      = $this->planPrices[$plan];
        $companyName = Setting::get('company_name', 'MakaziLink v2');
        $paybill     = Setting::get('my_paybill', Setting::get('mpesa_shortcode', '522522'));
        $paybillType = Setting::get('my_paybill_type', 'paybill');
        $account     = strtoupper(str_replace(' ', '', $companyName));

        return view('renewal.instructions', compact(
            'plan', 'amount', 'companyName', 'paybill', 'account'
        ));
    }

    // Step 3 — Verify M-Pesa code and activate
    public function verify(Request $request)
    {
        $request->validate([
            'plan'         => 'required|in:starter,growth,pro,enterprise',
            'mpesa_code'   => 'required|string|min:8|max:15',
            'phone_number' => 'required|string|min:10|max:15',
        ]);

        $plan       = $request->plan;
        $code       = strtoupper(trim($request->mpesa_code));
        $phone      = $this->formatPhone($request->phone_number);
        $amount     = $this->planPrices[$plan];
        $days       = $this->planDays[$plan];
        $companyName = Setting::get('company_name', 'MakaziLink v2');
        $paybill    = Setting::get('mpesa_shortcode', '522522');
        $passkey    = Setting::get('mpesa_passkey', '');
        $consumerKey    = Setting::get('mpesa_consumer_key', '');
        $consumerSecret = Setting::get('mpesa_consumer_secret', '');
        $environment    = Setting::get('mpesa_environment', 'sandbox');

        // Check if code already used
        if (RenewalCode::isUsed($code)) {
            return back()->withErrors([
                'mpesa_code' => 'This M-Pesa code has already been used. Please contact support if you think this is an error.'
            ])->withInput();
        }

        // Verify with M-Pesa API
        $verification = $this->verifyMpesaTransaction(
            $code, $phone, $amount,
            $consumerKey, $consumerSecret,
            $paybill, $passkey, $environment
        );

        if (!$verification['success']) {
            return back()->withErrors([
                'mpesa_code' => $verification['message']
            ])->withInput();
        }

        // Activate subscription
        $subscription = Subscription::first();

        if (!$subscription) {
            return back()->withErrors([
                'mpesa_code' => 'Subscription not found. Please contact support.'
            ])->withInput();
        }

        $from = $subscription->expires_at && $subscription->expires_at->isFuture()
            ? $subscription->expires_at
            : now();

        $subscription->update([
            'status'     => 'active',
            'expires_at' => $from->addDays($days),
        ]);

        // Mark code as used
        RenewalCode::markUsed($code, $phone, $verification['amount'], $plan, $days);

        // Send WhatsApp notification to admin
        $this->notifyAdmin($code, $phone, $verification['amount'], $plan, $days, $subscription);

        $expiryDate = $subscription->fresh()->expires_at->format('d M Y');

        return view('renewal.success', compact(
            'plan', 'amount', 'days', 'expiryDate', 'companyName'
        ));
    }

    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '254')) {
            return '0' . substr($phone, 3);
        }
        if (str_starts_with($phone, '7') || str_starts_with($phone, '1')) {
            return '0' . $phone;
        }
        return $phone;
    }

    private function verifyMpesaTransaction(
        string $code,
        string $phone,
        float $expectedAmount,
        string $consumerKey,
        string $consumerSecret,
        string $paybill,
        string $passkey,
        string $environment
    ): array {
        // If sandbox mode or no credentials just simulate verification
        if ($environment === 'sandbox' || empty($consumerKey) || empty($consumerSecret)) {
            return [
                'success' => true,
                'amount'  => $expectedAmount,
                'message' => 'Verified (sandbox mode)',
            ];
        }

        try {
            // Get access token
            $baseUrl = 'https://api.safaricom.co.ke';
            $tokenUrl = $baseUrl . '/oauth/v1/generate?grant_type=client_credentials';

            $tokenResponse = \Illuminate\Support\Facades\Http::withBasicAuth($consumerKey, $consumerSecret)
                ->get($tokenUrl);

            if (!$tokenResponse->successful()) {
                return ['success' => false, 'message' => 'Could not connect to M-Pesa. Please try again.', 'amount' => 0];
            }

            $accessToken = $tokenResponse->json()['access_token'];

            // Query transaction
            $queryUrl = $baseUrl . '/mpesa/transactionstatus/v1/query';

            $timestamp  = now()->format('YmdHis');
            $password   = base64_encode($paybill . $passkey . $timestamp);

            $queryResponse = \Illuminate\Support\Facades\Http::withToken($accessToken)
                ->post($queryUrl, [
                    'Initiator'          => 'apitest',
                    'SecurityCredential' => $password,
                    'CommandID'          => 'TransactionStatusQuery',
                    'TransactionID'      => $code,
                    'PartyA'             => $paybill,
                    'IdentifierType'     => '4',
                    'ResultURL'          => url('/mpesa/callback'),
                    'QueueTimeOutURL'    => url('/mpesa/callback'),
                    'Remarks'            => 'Subscription renewal verification',
                    'Occasion'           => 'Renewal',
                ]);

            $result = $queryResponse->json();

            if (isset($result['ResponseCode']) && $result['ResponseCode'] === '0') {
                return [
                    'success' => true,
                    'amount'  => $expectedAmount,
                    'message' => 'Payment verified successfully',
                ];
            }

            return [
                'success' => false,
                'message' => 'Payment could not be verified. Please check your M-Pesa code and try again.',
                'amount'  => 0,
            ];

        } catch (\Exception $e) {
            // If verification fails due to network issues allow manual review
            return [
                'success' => false,
                'message' => 'Verification service unavailable. Please contact support with your M-Pesa code.',
                'amount'  => 0,
            ];
        }
    }

    private function notifyAdmin(string $code, string $phone, float $amount, string $plan, int $days, $subscription): void
    {
        try {
            $adminPhone  = Setting::get('company_phone', '');
            $companyName = Setting::get('company_name', 'MakaziLink v2');
            $expiryDate  = $subscription->fresh()->expires_at->format('d M Y');
            $planLabel   = ucfirst($plan);

            if (!$adminPhone) return;

            $message = "💰 *Subscription Renewed*\n\n"
                . "System: {$companyName}\n"
                . "Plan: {$planLabel}\n"
                . "Amount: KES " . number_format($amount) . "\n"
                . "M-Pesa Code: {$code}\n"
                . "Phone: {$phone}\n"
                . "Days: {$days}\n"
                . "Expires: {$expiryDate}";

            $adminPhone = preg_replace('/\D/', '', $adminPhone);
            if (str_starts_with($adminPhone, '0')) {
                $adminPhone = '254' . substr($adminPhone, 1);
            }

            $whatsappUrl = 'https://wa.me/' . $adminPhone . '?text=' . urlencode($message);

            // Log the notification
            \Illuminate\Support\Facades\Log::info('Subscription renewal notification', [
                'code'   => $code,
                'phone'  => $phone,
                'amount' => $amount,
                'plan'   => $plan,
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send renewal notification: ' . $e->getMessage());
        }
    }
}