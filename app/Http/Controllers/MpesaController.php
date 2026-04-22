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

        // Format phone number
        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        }
        if (substr($phone, 0, 3) !== '254') {
            $phone = '254' . $phone;
        }

        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return back()->with('error', 'M-Pesa connection failed. Check your API keys in Settings.');
        }

        $shortcode  = Setting::get('mpesa_shortcode', '174379');
        $timestamp  = $this->getTimestamp();
        $password   = $this->getPassword();
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
                'AccountReference'  => $invoice->unit->unit_number,
                'TransactionDesc'   => 'Rent Payment - ' . $invoice->invoice_number,
            ]);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['ResponseCode']) && $data['ResponseCode'] === '0') {
                // Store checkout request ID in session for tracking
                session(['mpesa_checkout_id' => $data['CheckoutRequestID']]);
                session(['mpesa_invoice_id'  => $invoice->id]);

                return redirect()->route('mpesa.status')
                    ->with('success', 'Payment request sent to ' . $request->phone . '. Ask the tenant to check their phone and enter their M-Pesa PIN.');
            }
        }

        $error = $response->json('errorMessage') ?? 'Payment request failed. Please try again.';
        return back()->with('error', $error);
    }

    // Show payment status page
    public function status()
    {
        $invoiceId = session('mpesa_invoice_id');
        $invoice   = $invoiceId ? Invoice::with('tenant.user', 'unit')->find($invoiceId) : null;
        return view('mpesa.status', compact('invoice'));
    }

    // Handle M-Pesa callback (called by Safaricom after payment)
    public function callback(Request $request)
    {
        Log::info('M-Pesa Callback:', $request->all());

        $data = $request->input('Body.stkCallback');

        if (!$data) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        $resultCode = $data['ResultCode'];

        if ($resultCode === 0) {
            // Payment successful
            $items = collect($data['CallbackMetadata']['Item']);

            $amount          = $items->firstWhere('Name', 'Amount')['Value'] ?? 0;
            $mpesaReceiptNo  = $items->firstWhere('Name', 'MpesaReceiptNumber')['Value'] ?? '';
            $phone           = $items->firstWhere('Name', 'PhoneNumber')['Value'] ?? '';
            $checkoutId      = $data['CheckoutRequestID'];

            // Find the invoice from session or by matching
            $invoiceId = session('mpesa_invoice_id');

            if ($invoiceId) {
                $invoice = Invoice::find($invoiceId);

                if ($invoice) {
                    // Create payment record
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
                        'notes'                => 'M-Pesa STK Push - ' . $checkoutId,
                        'recorded_by'          => 1,
                    ]);

                    // Update invoice
                    $invoice->amount_paid += $amount;
                    $invoice->balance      = $invoice->total_amount - $invoice->amount_paid;
                    $invoice->status       = $invoice->balance <= 0 ? 'paid' : 'partial';
                    $invoice->save();
                }
            }
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    // Query payment status
    public function query(Request $request)
    {
        $checkoutId  = session('mpesa_checkout_id');
        $accessToken = $this->getAccessToken();

        if (!$accessToken || !$checkoutId) {
            return response()->json(['status' => 'unknown']);
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
            $resultCode = $data['ResultCode'] ?? null;

            if ($resultCode === '0' || $resultCode === 0) {
                return response()->json(['status' => 'success']);
            } elseif ($resultCode === '1032') {
                return response()->json(['status' => 'cancelled']);
            } else {
                return response()->json(['status' => 'pending']);
            }
        }

        return response()->json(['status' => 'pending']);
    }
}