<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Invoice::with('tenant.user', 'unit.property')
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Invoice No',
            'Tenant',
            'Unit',
            'Property',
            'Rent (KES)',
            'Water (KES)',
            'Garbage (KES)',
            'Other (KES)',
            'Total (KES)',
            'Amount Paid (KES)',
            'Balance (KES)',
            'Due Date',
            'Period',
            'Status',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            $invoice->tenant->user->name,
            $invoice->unit->unit_number,
            $invoice->unit->property->name,
            number_format($invoice->rent_amount, 2),
            number_format($invoice->water_amount, 2),
            number_format($invoice->garbage_amount, 2),
            number_format($invoice->other_amount, 2),
            number_format($invoice->total_amount, 2),
            number_format($invoice->amount_paid, 2),
            number_format($invoice->balance, 2),
            $invoice->due_date->format('d M Y'),
            $invoice->period_start->format('d M Y') . ' — ' . $invoice->period_end->format('d M Y'),
            ucfirst($invoice->status),
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