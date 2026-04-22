<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Property Report - {{ $property->name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            padding: 40px;
        }
        .header {
            border-bottom: 2px solid #1a7a4a;
            padding-bottom: 20px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .brand-name { font-size: 18px; font-weight: 700; color: #0f2d1e; }
        .brand-sub  { font-size: 10px; color: #6c757d; margin-top: 2px; }
        .brand-contact { font-size: 10px; color: #6c757d; margin-top: 6px; line-height: 1.6; }
        .report-title { text-align: right; }
        .report-title h1 { font-size: 20px; font-weight: 700; color: #1a7a4a; }
        .report-title .sub { font-size: 10px; color: #6c757d; margin-top: 4px; }
        .property-box {
            background: #f8fafc;
            padding: 14px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .property-box .name { font-size: 14px; font-weight: 700; color: #1a1a2e; }
        .property-box .sub  { font-size: 10px; color: #6c757d; margin-top: 2px; }
        .stats-row {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-box {
            flex: 1;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
        }
        .stat-box .s-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; color: #6c757d; margin-bottom: 4px; }
        .stat-box .s-value { font-size: 13px; font-weight: 700; }
        .bg-green { background: #dcfce7; } .c-green { color: #15803d; }
        .bg-blue  { background: #dbeafe; } .c-blue  { color: #1e40af; }
        .bg-amber { background: #fef3c7; } .c-amber { color: #b45309; }
        .bg-red   { background: #fee2e2; } .c-red   { color: #b91c1c; }
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e9ecef;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead th {
            background: #f8fafc;
            padding: 7px 10px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6c757d;
            border-bottom: 1px solid #e9ecef;
        }
        tbody td { padding: 8px 10px; border-bottom: 1px solid #f0f0f0; font-size: 10px; }
        .badge-occupied  { background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 10px; font-size: 9px; }
        .badge-vacant    { background: #fef3c7; color: #b45309; padding: 2px 8px; border-radius: 10px; font-size: 9px; }
        .badge-maint     { background: #fee2e2; color: #b91c1c; padding: 2px 8px; border-radius: 10px; font-size: 9px; }
        .footer {
            margin-top: 40px;
            padding-top: 12px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

@php
    $companyName    = \App\Models\Setting::get('company_name', 'MakaziLink v2');
    $companyPhone   = \App\Models\Setting::get('company_phone', '');
    $companyEmail   = \App\Models\Setting::get('company_email', '');
    $companyAddress = \App\Models\Setting::get('company_address', '');
    $currency       = \App\Models\Setting::get('currency', 'KES');
@endphp

<div class="header">
    <div>
        <div class="brand-name">{{ $companyName }}</div>
        <div class="brand-sub">Rental Management System</div>
        <div class="brand-contact">
            @if($companyPhone) {{ $companyPhone }}<br> @endif
            @if($companyEmail) {{ $companyEmail }}<br> @endif
            @if($companyAddress) {{ $companyAddress }} @endif
        </div>
    </div>
    <div class="report-title">
        <h1>PROPERTY REPORT</h1>
        <div class="sub">Generated: {{ now()->format('d M Y') }}</div>
    </div>
</div>

<div class="property-box">
    <div class="name">{{ $property->name }}</div>
    <div class="sub">{{ $property->address }}{{ $property->town ? ', ' . $property->town : '' }}{{ $property->county ? ', ' . $property->county : '' }}</div>
    <div class="sub">Type: {{ ucfirst(str_replace('_', ' ', $property->type)) }} | Owner: {{ $property->owner->name }}</div>
</div>

<div class="stats-row">
    <div class="stat-box bg-blue">
        <div class="s-label">Total Units</div>
        <div class="s-value c-blue">{{ $totalUnits }}</div>
    </div>
    <div class="stat-box bg-green">
        <div class="s-label">Occupied</div>
        <div class="s-value c-green">{{ $occupiedUnits }}</div>
    </div>
    <div class="stat-box bg-amber">
        <div class="s-label">Vacant</div>
        <div class="s-value c-amber">{{ $vacantUnits }}</div>
    </div>
    <div class="stat-box bg-green">
        <div class="s-label">Occupancy Rate</div>
        <div class="s-value c-green">{{ $occupancyRate }}%</div>
    </div>
    <div class="stat-box bg-green">
        <div class="s-label">This Month</div>
        <div class="s-value c-green">{{ $currency }} {{ number_format($thisMonthRevenue) }}</div>
    </div>
    <div class="stat-box {{ $outstandingBalance > 0 ? 'bg-red' : 'bg-green' }}">
        <div class="s-label">Outstanding</div>
        <div class="s-value {{ $outstandingBalance > 0 ? 'c-red' : 'c-green' }}">{{ $currency }} {{ number_format($outstandingBalance) }}</div>
    </div>
</div>

<div class="section-title">Units ({{ $totalUnits }})</div>
<table>
    <thead>
        <tr>
            <th>Unit</th>
            <th>Type</th>
            <th>Rent ({{ $currency }})</th>
            <th>Status</th>
            <th>Tenant</th>
            <th>Phone</th>
            <th>Lease Start</th>
        </tr>
    </thead>
    <tbody>
        @foreach($units as $unit)
        <tr>
            <td><strong>{{ $unit->unit_number }}</strong></td>
            <td>{{ ucfirst(str_replace('_', ' ', $unit->type)) }}</td>
            <td>{{ number_format($unit->rent_amount) }}</td>
            <td>
                @if($unit->status === 'occupied')
                    <span class="badge-occupied">Occupied</span>
                @elseif($unit->status === 'vacant')
                    <span class="badge-vacant">Vacant</span>
                @else
                    <span class="badge-maint">Maintenance</span>
                @endif
            </td>
            <td>{{ $unit->activeLease ? $unit->activeLease->tenant->user->name : '—' }}</td>
            <td>{{ $unit->activeLease ? $unit->activeLease->tenant->user->phone : '—' }}</td>
            <td>{{ $unit->activeLease ? $unit->activeLease->start_date->format('d M Y') : '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    {{ $companyName }} — Property Report generated on {{ now()->format('d M Y, h:i A') }}
</div>

</body>
</html>