<?php

namespace App\Exports;

use App\Models\Salary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalariesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Salary::with('recordedBy')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Staff Name',
            'Role',
            'Amount (KES)',
            'Payment Method',
            'Reference',
            'Month',
            'Payment Date',
            'Notes',
            'Recorded By',
        ];
    }

    public function map($salary): array
    {
        return [
            $salary->staff_name,
            $salary->role ?? '—',
            number_format($salary->amount, 2),
            ucfirst(str_replace('_', ' ', $salary->payment_method)),
            $salary->reference ?? '—',
            $salary->month_year,
            $salary->payment_date->format('d M Y'),
            $salary->notes ?? '—',
            $salary->recordedBy->name ?? '—',
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