<?php

namespace App\Exports;

use App\Models\Tenant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TenantsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Tenant::with('user', 'activeLease.unit.property')
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone',
            'ID Number',
            'Unit',
            'Property',
            'Monthly Rent (KES)',
            'Lease Start',
            'Lease End',
            'Balance (KES)',
            'Status',
        ];
    }

    public function map($tenant): array
    {
        $lease   = $tenant->activeLease;
        $balance = \App\Models\Invoice::where('tenant_id', $tenant->id)
            ->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])
            ->sum('balance');

        return [
            $tenant->user->name,
            $tenant->user->email,
            $tenant->user->phone,
            $tenant->id_number ?? '—',
            $lease ? $lease->unit->unit_number : '—',
            $lease ? $lease->unit->property->name : '—',
            $lease ? number_format($lease->monthly_rent, 2) : '—',
            $lease ? $lease->start_date->format('d M Y') : '—',
            $lease ? ($lease->end_date ? $lease->end_date->format('d M Y') : 'Open Ended') : '—',
            number_format($balance, 2),
            $lease ? 'Active' : 'No Lease',
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