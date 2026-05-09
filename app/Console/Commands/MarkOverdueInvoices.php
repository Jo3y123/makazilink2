<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\Setting;

class MarkOverdueInvoices extends Command
{
    protected $signature   = 'invoices:mark-overdue';
    protected $description = 'Mark unpaid invoices as overdue when past due date and send SMS reminders';

    public function handle()
    {
        $invoices = Invoice::with('tenant.user')
            ->whereIn('status', ['draft', 'sent', 'partial'])
            ->whereDate('due_date', '<', today())
            ->get();

        $count   = 0;
        $smsSent = 0;

        foreach ($invoices as $invoice) {
            $invoice->update(['status' => 'overdue']);
            $count++;

            // Send SMS reminder if enabled
            if (Setting::get('sms_on_overdue', '0') === '1') {
                $phone = $invoice->tenant->user->phone ?? '';
                $name  = $invoice->tenant->user->name ?? '';

                if ($phone) {
                    $sms = new \App\Services\SmsService();
                    $sent = $sms->sendOverdueReminder(
                        $phone,
                        $name,
                        $invoice->balance,
                        $invoice->invoice_number
                    );

                    if ($sent) {
                        $smsSent++;
                    }
                }
            }
        }

        $this->info("Marked {$count} invoice(s) as overdue. SMS sent: {$smsSent}.");

        \Illuminate\Support\Facades\Log::info('Overdue invoices marked', [
            'count'    => $count,
            'sms_sent' => $smsSent,
        ]);

        return Command::SUCCESS;
    }
}