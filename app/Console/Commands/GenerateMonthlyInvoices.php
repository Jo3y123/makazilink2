<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lease;
use App\Models\Invoice;
use App\Models\Setting;
use Carbon\Carbon;

class GenerateMonthlyInvoices extends Command
{
    protected $signature   = 'invoices:generate-monthly';
    protected $description = 'Automatically generate monthly invoices for all active tenants';

    public function handle()
    {
        $today      = now();
        $periodStart = $today->copy()->startOfMonth();
        $periodEnd   = $today->copy()->endOfMonth();
        $dueDate     = $today->copy()->startOfMonth()->addDays(
            (int) Setting::get('rent_due_day', 5) - 1
        );

        $leases  = Lease::with('tenant', 'unit')->where('status', 'active')->get();
        $created = 0;
        $skipped = 0;

        foreach ($leases as $lease) {
            // Check if invoice already exists for this period
            $exists = Invoice::where('lease_id', $lease->id)
                ->where('period_start', $periodStart->format('Y-m-d'))
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $unit    = $lease->unit;
            $water   = $unit->water_charge  ?? 0;
            $garbage = $unit->garbage_charge ?? 0;
            $service = $unit->service_charge ?? 0;

            // Check for latest metered water reading this period
            $waterReading = \App\Models\WaterReading::where('unit_id', $unit->id)
                ->whereMonth('reading_date', now()->month)
                ->whereYear('reading_date', now()->year)
                ->latest()
                ->first();

            if ($waterReading && $waterReading->amount_charged > 0) {
                $water = $waterReading->amount_charged;
            }

            $rent  = $lease->monthly_rent;
            $total = $rent + $water + $garbage + $service;

            // Add late penalty if tenant has overdue balance
            $latePenalty = 0;
            $penalty     = (float) Setting::get('late_payment_penalty', 0);
            if ($penalty > 0) {
                $hasOverdue = \App\Models\Invoice::where('tenant_id', $lease->tenant_id)
                    ->where('status', 'overdue')
                    ->exists();
                if ($hasOverdue) {
                    $latePenalty = $penalty;
                    $total      += $latePenalty;
                }
            }

            Invoice::create([
                'invoice_number' => Invoice::generateNumber(),
                'lease_id'       => $lease->id,
                'tenant_id'      => $lease->tenant_id,
                'unit_id'        => $lease->unit_id,
                'rent_amount'    => $rent,
                'water_amount'   => $water,
                'garbage_amount' => $garbage,
                'other_amount'   => $service + $latePenalty,
                'total_amount'   => $total,
                'amount_paid'    => 0,
                'balance'        => $total,
                'due_date'       => $dueDate,
                'period_start'   => $periodStart,
                'period_end'     => $periodEnd,
                'status'         => 'draft',
                'notes'          => Setting::get('invoice_notes', ''),
            ]);

            $created++;

            // Send SMS if enabled
            if (\App\Models\Setting::get('sms_on_invoice', '0') === '1') {
                $phone = $lease->tenant->user->phone ?? '';
                $name  = $lease->tenant->user->name ?? '';
                if ($phone) {
                    $sms = new \App\Services\SmsService();
                    $sms->sendInvoiceNotification(
                        $phone,
                        $name,
                        'INV-' . str_pad($created, 5, '0', STR_PAD_LEFT),
                        $lease->monthly_rent,
                        $dueDate->format('d M Y')
                    );
                }
            }
        }

        $this->info("Monthly invoices generated. Created: {$created}, Skipped: {$skipped}.");

        \Illuminate\Support\Facades\Log::info('Monthly invoices generated', [
            'created' => $created,
            'skipped' => $skipped,
            'month'   => $today->format('F Y'),
        ]);
    }
}