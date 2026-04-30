<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\Unit;
 
class MpesaController extends Controller
{
    private function getBaseUrl(): string
    {
        $env = Setting::get('mpesa_environment', 'sandbox');
        return $env === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }
 
    private function getAccessToken(): string|null
    {
        $consumerKey    = Setting::get('mpesa_consumer_key');
        $consumerSecret = Setting::get('mpesa_consumer_secret');
 
        if (!$consumerKey || !$consumerSecret) {
            return null;
        }
 
        $url      = $this->getBaseUrl() . '/oauth/v1/generate?grant_type=client_credentials';
        $response = Http::withBasicAuth($consumerKey, $consumerSecret)->get($url);
 
        if ($response->successful()) {
            return $response->json('access_token');
        }
 
        return null;
    }
 
    private function getPassword(): string
    {
        $shortcode = Setting::get('mpesa_shortcode', '174379');
        $passkey   = Setting::get('mpesa_passkey');
        $timestamp = now()->format('YmdHis');
        return base64_encode($shortcode . $passkey . $timestamp);
    }
 
    private function getTimestamp(): string
    {
        return now()->format('YmdHis');
    }
 
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }
        if (!str_starts_with($phone, '254')) {
            $phone = '254' . $phone;
        }
        return $phone;
    }
 
    // Show STK push form
    public function showPush(Invoice $invoice)
    {
        $invoice->load('tenant.user', 'unit.property');
        return view('mpesa.push', compact('invoice'));
    }
 
    // Send STK Push to tenant phone
    public function sendPush(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'phone'      => 'required|string',
            'amount'     => 'required|numeric|min:1',
        ]);
 
        $invoice = Invoice::with('tenant.user', 'unit')->find($request->invoice_id);
        $phone   = $this->formatPhone($request->phone);
 
        $accessToken = $this->getAccessToken();
 
        if (!$accessToken) {
            return back()->with('error', 'M-Pesa connection failed. Check your API keys in Settings.');
        }
 
        $shortcode   = Setting::get('mpesa_shortcode', '174379');
        $timestamp   = $this->getTimestamp();
        $password    = $this->getPassword();
        $callbackUrl = Setting::get('mpesa_callback_url', url('/mpesa/callback'));
 
        $response = Http::withToken($accessToken)
            ->post($this->getBaseUrl() . '/mpesa/stkpush/v1/processrequest', [
                'BusinessShortCode' => $shortcode,
                'Password'          => $password,
                'Timestamp'         => $timestamp,
                'TransactionType'   => 'CustomerPayBillOnline',
                'Amount'            => (int) $request->amount,
                'PartyA'            => $phone,
                'PartyB'            => $shortcode,
                'PhoneNumber'       => $phone,
                'CallBackURL'       => $callbackUrl,
                'AccountReference'  => $invoice->invoice_number,
                'TransactionDesc'   => 'Rent Payment - ' . $invoice->invoice_number,
            ]);
 
        if ($response->successful()) {
            $data = $response->json();
 
            if (isset($data['ResponseCode']) && $data['ResponseCode'] === '0') {
                $checkoutId = $data['CheckoutRequestID'];
 
                // Store in cache instead of session so callback can access it
                cache()->put('mpesa_checkout_' . $checkoutId, [
                    'invoice_id' => $invoice->id,
                    'amount'     => $request->amount,
                    'phone'      => $phone,
                ], now()->addMinutes(10));
 
                // Store in session for status polling
                session([
                    'mpesa_checkout_id' => $checkoutId,
                    'mpesa_invoice_id'  => $invoice->id,
                    'mpesa_phone'       => $request->phone,
                    'mpesa_amount'      => $request->amount,
                ]);
 
                return redirect()->route('mpesa.status');
            }
 
            // Handle specific M-Pesa error codes
            $errorCode    = $response->json('errorCode') ?? '';
            $errorMessage = $this->getMpesaErrorMessage($errorCode, $response->json('errorMessage'));
            return back()->with('error', $errorMessage);
        }
 
        return back()->with('error', 'Could not connect to M-Pesa. Please try again.');
    }
 
    // Get friendly error message
    private function getMpesaErrorMessage(string $code, ?string $default): string
    {
        return match($code) {
            '400.002.02' => 'Bad request. Please check the phone number and try again.',
            '404.001.04' => 'M-Pesa service not found. Contact support.',
            '500.001.1001' => 'M-Pesa servers are temporarily unavailable. Please try again in a few minutes.',
            default => $default ?? 'Payment request failed. Please try again.',
        };
    }
 
    // Show payment status page with auto polling
    public function status()
    {
        $checkoutId = session('mpesa_checkout_id');
        $invoiceId  = session('mpesa_invoice_id');
        $phone      = session('mpesa_phone');
        $amount     = session('mpesa_amount');
        $invoice    = $invoiceId ? Invoice::with('tenant.user', 'unit')->find($invoiceId) : null;
 
        return view('mpesa.status', compact('invoice', 'checkoutId', 'phone', 'amount'));
    }
 
    // Handle M-Pesa callback (called by Safaricom after payment)
    public function callback(Request $request)
    {
        Log::info('M-Pesa Callback received:', $request->all());
 
        $data = $request->input('Body.stkCallback');
 
        if (!$data) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }
 
        $resultCode = $data['ResultCode'];
        $checkoutId = $data['CheckoutRequestID'];
 
        // Store result in cache for polling
        cache()->put('mpesa_result_' . $checkoutId, [
            'result_code' => $resultCode,
            'result_desc' => $data['ResultDesc'] ?? '',
        ], now()->addMinutes(10));
 
        if ((int) $resultCode === 0) {
            // Payment successful
            $items = collect($data['CallbackMetadata']['Item'] ?? []);
 
            $amount         = $items->firstWhere('Name', 'Amount')['Value'] ?? 0;
            $mpesaReceiptNo = $items->firstWhere('Name', 'MpesaReceiptNumber')['Value'] ?? '';
            $phone          = $items->firstWhere('Name', 'PhoneNumber')['Value'] ?? '';
 
            // Get invoice from cache
            $cached = cache()->get('mpesa_checkout_' . $checkoutId);
 
            if ($cached && isset($cached['invoice_id'])) {
                $invoice = Invoice::find($cached['invoice_id']);
 
                if ($invoice) {
                    // Prevent duplicate payment
                    $exists = Payment::where('mpesa_transaction_id', $mpesaReceiptNo)->exists();
 
                    if (!$exists) {
                        // Record payment
                        $payment = Payment::create([
                            'receipt_number'       => Payment::generateReceiptNumber(),
                            'invoice_id'           => $invoice->id,
                            'tenant_id'            => $invoice->tenant_id,
                            'unit_id'              => $invoice->unit_id,
                            'amount'               => $amount,
                            'payment_method'       => 'mpesa',
                            'mpesa_transaction_id' => $mpesaReceiptNo,
                            'payment_date'         => now(),
                            'status'               => 'confirmed',
                            'notes'                => 'M-Pesa STK Push auto-recorded',
                            'recorded_by'          => 1,
                        ]);
 
                        // Update invoice
                        $invoice->amount_paid = (float) $invoice->amount_paid + (float) $amount;
                        $invoice->balance     = (float) $invoice->total_amount - (float) $invoice->amount_paid;
                        $invoice->status      = $invoice->balance <= 0 ? 'paid' : 'partial';
                        $invoice->save();
 
                        // Send WhatsApp receipt to tenant
                        $this->sendWhatsAppReceipt($invoice, $payment);
 
                        // Notify admin
                        $this->notifyAdmin($invoice, $payment);
 
                        Log::info('M-Pesa payment recorded successfully', [
                            'receipt'  => $mpesaReceiptNo,
                            'invoice'  => $invoice->invoice_number,
                            'amount'   => $amount,
                        ]);
                    } else {
                        Log::warning('Duplicate M-Pesa payment ignored', ['receipt' => $mpesaReceiptNo]);
                    }
                }
            }
        } else {
            // Payment failed — log reason
            Log::warning('M-Pesa payment failed', [
                'checkout_id' => $checkoutId,
                'result_code' => $resultCode,
                'result_desc' => $data['ResultDesc'] ?? '',
            ]);
        }
 
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }
 
    // Query payment status — called by frontend polling
    public function query(Request $request)
    {
        $checkoutId = session('mpesa_checkout_id');
 
        if (!$checkoutId) {
            return response()->json(['status' => 'unknown', 'message' => 'No pending payment found.']);
        }
 
        // First check cache for callback result
        $cached = cache()->get('mpesa_result_' . $checkoutId);
 
        if ($cached) {
            $resultCode = (int) $cached['result_code'];
 
            if ($resultCode === 0) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Payment confirmed successfully!',
                ]);
            }
 
            $message = $this->getCallbackErrorMessage($resultCode);
            return response()->json([
                'status'  => 'failed',
                'message' => $message,
                'code'    => $resultCode,
            ]);
        }
 
        // No callback yet — query M-Pesa directly
        $accessToken = $this->getAccessToken();
 
        if (!$accessToken) {
            return response()->json(['status' => 'pending', 'message' => 'Waiting for payment...']);
        }
 
        $shortcode = Setting::get('mpesa_shortcode', '174379');
        $timestamp = $this->getTimestamp();
        $password  = $this->getPassword();
 
        $response = Http::withToken($accessToken)
            ->post($this->getBaseUrl() . '/mpesa/stkpushquery/v1/query', [
                'BusinessShortCode' => $shortcode,
                'Password'          => $password,
                'Timestamp'         => $timestamp,
                'CheckoutRequestID' => $checkoutId,
            ]);
 
        if ($response->successful()) {
            $data       = $response->json();
            $resultCode = isset($data['ResultCode']) ? (int) $data['ResultCode'] : null;
 
            if ($resultCode === 0) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Payment confirmed successfully!',
                ]);
            } elseif ($resultCode === 1032) {
                return response()->json([
                    'status'  => 'cancelled',
                    'message' => 'Payment was cancelled by the tenant.',
                ]);
            } elseif ($resultCode === 1037) {
                return response()->json([
                    'status'  => 'timeout',
                    'message' => 'Payment request timed out. Tenant did not respond.',
                ]);
            } elseif ($resultCode === 1) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Payment failed. Tenant may have insufficient balance.',
                ]);
            }
        }
 
        return response()->json(['status' => 'pending', 'message' => 'Waiting for tenant to pay...']);
    }
 
    // Get friendly message from callback result code
    private function getCallbackErrorMessage(int $code): string
    {
        return match($code) {
            1032 => 'Payment was cancelled by the tenant.',
            1037 => 'Payment request timed out. Tenant did not respond.',
            1    => 'Payment failed. Tenant may have insufficient M-Pesa balance.',
            2001 => 'Wrong PIN entered. Tenant should try again.',
            default => 'Payment failed. Please try again.',
        };
    }
 
    // Send WhatsApp receipt to tenant after successful payment
    private function sendWhatsAppReceipt(Invoice $invoice, Payment $payment): void
    {
        try {
            $invoice->load('tenant.user', 'unit.property');
            $currency    = Setting::get('currency', 'KES');
            $companyName = Setting::get('company_name', 'MakaziLink v2');
            $tenantPhone = $invoice->tenant->user->phone;
 
            if (!$tenantPhone) return;
 
            $phone = preg_replace('/[^0-9]/', '', $tenantPhone);
            if (str_starts_with($phone, '0')) {
                $phone = '254' . substr($phone, 1);
            }
 
            $message = "✅ *Payment Receipt*\n\n"
                . "Dear {$invoice->tenant->user->name},\n\n"
                . "Your payment has been received.\n\n"
                . "Receipt No: {$payment->receipt_number}\n"
                . "Invoice: {$invoice->invoice_number}\n"
                . "Amount: {$currency} " . number_format($payment->amount) . "\n"
                . "M-Pesa Code: {$payment->mpesa_transaction_id}\n"
                . "Date: " . now()->format('d M Y, h:i A') . "\n"
                . "Balance: {$currency} " . number_format($invoice->fresh()->balance) . "\n\n"
                . "Thank you for your payment.\n"
                . "— {$companyName}";
 
            Log::info('WhatsApp receipt prepared for tenant', [
                'phone'   => $phone,
                'receipt' => $payment->receipt_number,
            ]);
 
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp receipt: ' . $e->getMessage());
        }
    }
 
    // Notify admin of successful payment
    private function notifyAdmin(Invoice $invoice, Payment $payment): void
    {
        try {
            $adminPhone  = Setting::get('company_phone', '');
            $currency    = Setting::get('currency', 'KES');
            $companyName = Setting::get('company_name', 'MakaziLink v2');
 
            if (!$adminPhone) return;
 
            $message = "💰 *Payment Received*\n\n"
                . "Tenant: {$invoice->tenant->user->name}\n"
                . "Unit: {$invoice->unit->unit_number}\n"
                . "Invoice: {$invoice->invoice_number}\n"
                . "Amount: {$currency} " . number_format($payment->amount) . "\n"
                . "M-Pesa: {$payment->mpesa_transaction_id}\n"
                . "Balance: {$currency} " . number_format($invoice->fresh()->balance) . "\n"
                . "Date: " . now()->format('d M Y, h:i A');
 
            Log::info('Admin payment notification prepared', [
                'admin_phone' => $adminPhone,
                'payment'     => $payment->receipt_number,
            ]);
 
        } catch (\Exception $e) {
            Log::error('Failed to notify admin: ' . $e->getMessage());
        }
    }
    // Tenant self-service payment
public function tenantPay(Request $request)
{
    $request->validate([
        'invoice_id' => 'required|exists:invoices,id',
        'phone'      => 'required|string',
    ]);

    $user    = auth()->user();
    $tenant  = $user->tenant;
    $invoice = Invoice::where('id', $request->invoice_id)
        ->where('tenant_id', $tenant->id)
        ->first();

    if (!$invoice) {
        return back()->with('error', 'Invoice not found.');
    }

    $phone       = $this->formatPhone($request->phone);
    $accessToken = $this->getAccessToken();

    if (!$accessToken) {
        return back()->with('error', 'M-Pesa is not configured. Please contact your landlord.');
    }

    $shortcode   = Setting::get('mpesa_shortcode', '174379');
    $timestamp   = $this->getTimestamp();
    $password    = $this->getPassword();
    $callbackUrl = Setting::get('mpesa_callback_url', url('/mpesa/callback'));

    $response = Http::withToken($accessToken)
        ->post($this->getBaseUrl() . '/mpesa/stkpush/v1/processrequest', [
            'BusinessShortCode' => $shortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => 'CustomerPayBillOnline',
            'Amount'            => (int) $invoice->balance,
            'PartyA'            => $phone,
            'PartyB'            => $shortcode,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => $callbackUrl,
            'AccountReference'  => $invoice->invoice_number,
            'TransactionDesc'   => 'Rent Payment - ' . $invoice->invoice_number,
        ]);

    if ($response->successful()) {
        $data = $response->json();

        if (isset($data['ResponseCode']) && $data['ResponseCode'] === '0') {
            $checkoutId = $data['CheckoutRequestID'];

            cache()->put('mpesa_checkout_' . $checkoutId, [
                'invoice_id' => $invoice->id,
                'amount'     => $invoice->balance,
                'phone'      => $phone,
            ], now()->addMinutes(10));

            session([
                'mpesa_checkout_id' => $checkoutId,
                'mpesa_invoice_id'  => $invoice->id,
                'mpesa_phone'       => $request->phone,
                'mpesa_amount'      => $invoice->balance,
            ]);

            return redirect()->route('tenant.pay.status');
        }
    }

    $error = $response->json('errorMessage') ?? 'Payment request failed. Please try again.';
    return back()->with('error', $error);
    }

    // Tenant payment status page
    public function tenantPayStatus()
    {
        $checkoutId = session('mpesa_checkout_id');
        $invoiceId  = session('mpesa_invoice_id');
        $phone      = session('mpesa_phone');
        $amount     = session('mpesa_amount');
        $invoice    = $invoiceId ? Invoice::with('unit.property')->find($invoiceId) : null;

        return view('tenant.pay-status', compact('invoice', 'checkoutId', 'phone', 'amount'));
    }
}
 