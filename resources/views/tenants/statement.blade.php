<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Statement - {{ $tenant->user->name }}</title>
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
        .brand-name {
            font-size: 18px;
            font-weight: 700;
            color: #0f2d1e;
        }
        .brand-sub {
            font-size: 10px;
            color: #6c757d;
            margin-top: 2px;
        }
        .brand-contact {
            font-size: 10px;
            color: #6c757d;
            margin-top: 6px;
            line-height: 1.6;
        }
        .statement-title {
            text-align: right;
        }
        .statement-title h1 {
            font-size: 20px;
            font-weight: 700;
            color: #1a7a4a;
        }
        .statement-title .date {
            font-size: 10px;
            color: #6c757d;
            margin-top: 4px;
        }
        .tenant-box {
            background: #f8fafc;
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
        }
        .tenant-box .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6c757d;
            margin-bottom: 4px;
        }
        .tenant-box .value {
            font-size: 12px;
            font-weight: 600;
            color: #1a1a2e;
        }
        .tenant-box .sub {
            font-size: 10px;
            color: #6c757d;
        }
        .summary-boxes {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        .summary-box {
            flex: 1;
            padding: 14px;
            border-radius: 6px;
            text-align: center;
        }
        .summary-box .s-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6c757d;
            margin-bottom: 4px;
        }
        .summary-box .s-value {
            font-size: 14px;
            font-weight: 700;
        }
        .bg-green  { background: #dcfce7; }
        .bg-blue   { background: #dbeafe; }
        .bg-red    { background: #fee2e2; }
        .c-green   { color: #15803d; }
        .c-blue    { color: #1e40af; }
        .c-red     { color: #b91c1c; }
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e9ecef;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
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
        tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 11px;
        }
        .text-right { text-align: right; }
        .badge-paid    { background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 10px; font-size: 9px; }
        .badge-partial { background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 10px; font-size: 9px; }
        .badge-overdue { background: #fee2e2; color: #b91c1c; padding: 2px 8px; border-radius: 10px; font-size: 9px; }
        .badge-draft   { background: #f1f5f9; color: #64748b; padding: 2px 8px; border-radius: 10px; font-size: 9px; }
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
    <div class="statement-title">
        <h1>TENANT STATEMENT</h1>
        <div class="date">Generated: {{ now()->format('d M Y') }}</div>
    </div>
</div>

<div class="tenant-box">
    <div>
        <div class="label">Tenant</div>
        <div class="value">{{ $tenant->user->name }}</div>
        <div class="sub">{{ $tenant->user->email }}</div>
        <div class="sub">{{ $tenant->user->phone }}</div>
    </div>
    <div>
        <div class="label">ID Number</div>
        <div class="value">{{ $tenant->id_number ?? 'N/A' }}</div>
        <div class="label" style="margin-top:8px">Occupation</div>
        <div class="value">{{ $tenant->occupation ?? 'N/A' }}</div>
    </div>
    <div style="text-align:right">
        @if($tenant->activeLease)
        <div class="label">Current Unit</div>
        <div class="value">{{ $tenant->activeLease->unit->unit_number }}</div>
        <div class="sub">{{ $tenant->activeLease->unit->property->name }}</div>
        <div class="label" style="margin-top:8px">Monthly Rent</div>
        <div class="value">{{ $currency }} {{ number_format($tenant->activeLease->monthly_rent) }}</div>
        @endif
    </div>
</div>

<div class="summary-boxes">
    <div class="summary-box bg-blue">
        <div class="s-label">Total Charged</div>
        <div class="s-value c-blue">{{ $currency }} {{ number_format($totalCharged) }}</div>
    </div>
    <div class="summary-box bg-green">
        <div class="s-label">Total Paid</div>
        <div class="s-value c-green">{{ $currency }} {{ number_format($totalPaid) }}</div>
    </div>
    <div class="summary-box {{ $balance > 0 ? 'bg-red' : 'bg-green' }}">
        <div class="s-label">Balance</div>
        <div class="s-value {{ $balance > 0 ? 'c-red' : 'c-green' }}">{{ $currency }} {{ number_format($balance) }}</div>
    </div>
</div>

{{-- Invoices --}}
<div class="section-title">Invoices ({{ $invoices->count() }})</div>
@if($invoices->isEmpty())
    <p style="color:#6c757d;font-size:10px;margin-bottom:24px">No invoices found.</p>
@else
<table>
    <thead>
        <tr>
            <th>Invoice #</th>
            <th>Period</th>
            <th>Due Date</th>
            <th class="text-right">Total</th>
            <th class="text-right">Paid</th>
            <th class="text-right">Balance</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $invoice)
        <tr>
            <td>{{ $invoice->invoice_number }}</td>
            <td>{{ $invoice->period_start->format('d M Y') }} — {{ $invoice->period_end->format('d M Y') }}</td>
            <td>{{ $invoice->due_date->format('d M Y') }}</td>
            <td class="text-right">{{ number_format($invoice->total_amount) }}</td>
            <td class="text-right" style="color:#15803d">{{ number_format($invoice->amount_paid) }}</td>
            <td class="text-right" style="color:{{ $invoice->balance > 0 ? '#b91c1c' : '#15803d' }}">
                {{ number_format($invoice->balance) }}
            </td>
            <td>
                @if($invoice->status === 'paid')
                    <span class="badge-paid">Paid</span>
                @elseif($invoice->status === 'partial')
                    <span class="badge-partial">Partial</span>
                @elseif($invoice->status === 'overdue')
                    <span class="badge-overdue">Overdue</span>
                @else
                    <span class="badge-draft">{{ ucfirst($invoice->status) }}</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Payments --}}
<div class="section-title">Payments ({{ $payments->count() }})</div>
@if($payments->isEmpty())
    <p style="color:#6c757d;font-size:10px;margin-bottom:24px">No payments found.</p>
@else
<table>
    <thead>
        <tr>
            <th>Receipt #</th>
            <th>Date</th>
            <th>Method</th>
            <th>Reference</th>
            <th class="text-right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payments as $payment)
        <tr>
            <td>{{ $payment->receipt_number }}</td>
            <td>{{ $payment->payment_date->format('d M Y') }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
            <td>{{ $payment->mpesa_transaction_id ?? $payment->reference_number ?? '—' }}</td>
            <td class="text-right" style="color:#15803d;font-weight:600">
                {{ number_format($payment->amount) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="footer">
    {{ $companyName }} — Tenant Statement generated on {{ now()->format('d M Y, h:i A') }}
</div>

</body>
</html>