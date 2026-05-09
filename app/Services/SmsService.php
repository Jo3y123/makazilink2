<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private string $username;
    private string $apiKey;
    private string $senderId;
    private bool   $enabled;

    public function __construct()
    {
        $this->username = Setting::get('africastalking_username', '');
        $this->apiKey   = Setting::get('africastalking_api_key', '');
        $this->senderId = Setting::get('africastalking_sender_id', 'MAKAZILINK');
        $this->enabled  = !empty($this->username) && !empty($this->apiKey);
    }

    /**
     * Send SMS to a single phone number
     */
    public function send(string $phone, string $message): bool
    {
        if (!$this->enabled) {
            Log::info('SMS not sent — Africa\'s Talking credentials not configured', [
                'phone'   => $phone,
                'message' => $message,
            ]);
            return false;
        }

        $phone = $this->formatPhone($phone);

        if (!$phone) {
            Log::warning('SMS not sent — invalid phone number', ['phone' => $phone]);
            return false;
        }

        try {
            $AT       = new \AfricasTalking\SDK\AfricasTalking($this->username, $this->apiKey);
            $sms      = $AT->sms();
            $response = $sms->send([
                'to'      => $phone,
                'message' => $message,
                'from'    => $this->senderId,
            ]);

            Log::info('SMS sent successfully', [
                'phone'    => $phone,
                'message'  => $message,
                'response' => $response,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('SMS failed', [
                'phone'   => $phone,
                'message' => $message,
                'error'   => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send SMS to multiple phone numbers
     */
    public function sendBulk(array $phones, string $message): bool
    {
        if (!$this->enabled) {
            Log::info('Bulk SMS not sent — Africa\'s Talking credentials not configured');
            return false;
        }

        $formatted = array_filter(array_map([$this, 'formatPhone'], $phones));

        if (empty($formatted)) {
            return false;
        }

        try {
            $AT       = new \AfricasTalking\SDK\AfricasTalking($this->username, $this->apiKey);
            $sms      = $AT->sms();
            $response = $sms->send([
                'to'      => implode(',', $formatted),
                'message' => $message,
                'from'    => $this->senderId,
            ]);

            Log::info('Bulk SMS sent', [
                'count'    => count($formatted),
                'response' => $response,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Bulk SMS failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send invoice notification to tenant
     */
    public function sendInvoiceNotification(string $phone, string $name, string $invoiceNumber, float $amount, string $dueDate): bool
    {
        $currency = Setting::get('currency', 'KES');
        $company  = Setting::get('company_name', 'MakaziLink');

        $message = "Dear {$name}, your rent invoice of {$currency} " . number_format($amount) . " is due on {$dueDate}. Ref: {$invoiceNumber}. - {$company}";

        return $this->send($phone, $message);
    }

    /**
     * Send payment confirmation to tenant
     */
    public function sendPaymentConfirmation(string $phone, string $name, string $receiptNumber, float $amount): bool
    {
        $currency = Setting::get('currency', 'KES');
        $company  = Setting::get('company_name', 'MakaziLink');

        $message = "Dear {$name}, payment of {$currency} " . number_format($amount) . " received. Receipt: {$receiptNumber}. Thank you. - {$company}";

        return $this->send($phone, $message);
    }

    /**
     * Send overdue reminder to tenant
     */
    public function sendOverdueReminder(string $phone, string $name, float $amount, string $invoiceNumber): bool
    {
        $currency = Setting::get('currency', 'KES');
        $company  = Setting::get('company_name', 'MakaziLink');

        $message = "Dear {$name}, your rent of {$currency} " . number_format($amount) . " (Ref: {$invoiceNumber}) is overdue. Please pay to avoid inconvenience. - {$company}";

        return $this->send($phone, $message);
    }

    /**
     * Send lease expiry reminder
     */
    public function sendLeaseExpiryReminder(string $phone, string $name, string $expiryDate): bool
    {
        $company = Setting::get('company_name', 'MakaziLink');

        $message = "Dear {$name}, your lease expires on {$expiryDate}. Please contact us to discuss renewal. - {$company}";

        return $this->send($phone, $message);
    }

    /**
     * Format phone to international format
     */
    private function formatPhone(string $phone): ?string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (empty($phone)) {
            return null;
        }

        // Already in international format
        if (str_starts_with($phone, '254') && strlen($phone) === 12) {
            return '+' . $phone;
        }

        // Local format 07xx or 01xx
        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            return '+254' . substr($phone, 1);
        }

        // Without leading zero 7xx or 1xx
        if ((str_starts_with($phone, '7') || str_starts_with($phone, '1')) && strlen($phone) === 9) {
            return '+254' . $phone;
        }

        return null;
    }

    /**
     * Check if SMS is configured
     */
    public function isConfigured(): bool
    {
        return $this->enabled;
    }
}