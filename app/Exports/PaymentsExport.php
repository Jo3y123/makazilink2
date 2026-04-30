<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Payment::with('tenant.user', 'unit.property')
            ->where('status', 'confirmed')
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Receipt No',
            'Tenant',
            'Unit',
            'Property',
            'Amount (KES)',
            'Payment Method',
            'M-Pesa Code',
            'Payment Date',
            'Status',
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->receipt_number,
            $payment->tenant->user->name,
            $payment->unit->unit_number,
            $payment->unit->property->name,
            number_format($payment->amount, 2),
            ucfirst(str_replace('_', ' ', $payment->payment_method)),
            $payment->mpesa_transaction_id ?? '—',
            $payment->payment_date->format('d M Y'),
            ucfirst($payment->status),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1a7a4a']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}