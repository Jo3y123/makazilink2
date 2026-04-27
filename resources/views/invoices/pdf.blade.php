<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1a1a2e;
            padding: 40px;
        }
        .brand-name {
            font-size: 20px;
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
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 6px;
        }
        .status-paid    { background: #dcfce7; color: #15803d; }
        .status-partial { background: #dbeafe; color: #1e40af; }
        .status-overdue { background: #fee2e2; color: #b91c1c; }
        .status-draft   { background: #f1f5f9; color: #64748b; }
        .status-sent    { background: #f3e8ff; color: #7e22ce; }
        .details-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6c757d;
            margin-bottom: 4px;
        }
        .details-value {
            font-size: 12px;
            font-weight: 600;
            color: #1a1a2e;
        }
        .details-sub {
            font-size: 10px;
            color: #6c757d;
            margin-top: 2px;
        }
        .unit-box {
            background: #f8fafc;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 24px;
            font-size: 11px;
            color: #374151;
        }
        table.lines {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.lines thead th {
            background: #f8fafc;
            padding: 8px 12px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6c757d;
            border-bottom: 1px solid #e9ecef;
        }
        table.lines tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 12px;
        }
        .text-right { text-align: right; }
        .totals-row td {
            padding: 8px 12px;
            font-size: 12px;
            border: none;
        }
        .totals-row.grand-total td {
            font-weight: 700;
            font-size: 14px;
            border-top: 2px solid #e9ecef;
            padding-top: 12px;
        }
        .balance-row td {
            color: #b91c1c;
            font-weight: 700;
            font-size: 13px;
            border: none;
            padding: 8px 12px;
        }
        .invoice-notes {
            background: #f8fafc;
            padding: 12px;
            border-radius: 6px;
            font-size: 10px;
            color: #6c757d;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 16px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            font-size: 10px;
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
    $invoiceNotes   = \App\Models\Setting::get('invoice_notes', '');
    $logoPath       = \App\Models\Setting::get('logo_path', '');
@endphp
 
{{-- Header --}}
<table style="width:100%;margin-bottom:30px;padding-bottom:20px;border-bottom:2px solid #1a7a4a;border-collapse:collapse">
    <tr>
        <td style="vertical-align:top;width:60%">
            @if($logoPath)
                <img src="{{ public_path('storage/' . $logoPath) }}"
                     alt="Logo"
                     style="height:40px;width:auto;margin-bottom:6px;display:block">
            @endif
            <div class="brand-name">{{ $companyName }}</div>
            <div class="brand-sub">Rental Management System</div>
            <div class="brand-contact">
                @if($companyPhone) {{ $companyPhone }}<br> @endif
                @if($companyEmail) {{ $companyEmail }}<br> @endif
                @if($companyAddress) {{ $companyAddress }} @endif
            </div>
        </td>
        <td style="vertical-align:top;text-align:right;width:40%">
            <div style="font-size:24px;font-weight:700;color:#1a7a4a">INVOICE</div>
            <div style="font-size:12px;color:#6c757d;margin-top:4px">{{ $invoice->invoice_number }}</div>
            <div>
                <span class="status-badge status-{{ $invoice->status }}">
                    {{ strtoupper($invoice->status) }}
                </span>
            </div>
        </td>
    </tr>
</table>
 
{{-- Billed To --}}
<table style="width:100%;margin-bottom:16px;border-collapse:collapse">
    <tr>
        <td style="vertical-align:top;width:50%">
            <div class="details-label">Billed To</div>
            <div class="details-value">{{ $invoice->tenant->user->name }}</div>
            <div class="details-sub">{{ $invoice->tenant->user->email }}</div>
            <div class="details-sub">{{ $invoice->tenant->user->phone }}</div>
        </td>
        <td style="vertical-align:top;width:50%">
            <div class="details-label">Invoice Number</div>
            <div class="details-value">{{ $invoice->invoice_number }}</div>
        </td>
    </tr>
</table>

{{-- Period and Due Date --}}
<table style="width:100%;margin-bottom:24px;border-collapse:collapse">
    <tr>
        <td style="vertical-align:top;width:50%">
            <div class="details-label">Billing Period</div>
            <div class="details-value">
                {{ $invoice->period_start->format('d M Y') }} —
                {{ $invoice->period_end->format('d M Y') }}
            </div>
        </td>
        <td style="vertical-align:top;width:50%">
            <div class="details-label">Due Date</div>
            <div class="details-value" style="color:#b91c1c">
                {{ $invoice->due_date->format('d M Y') }}
            </div>
        </td>
    </tr>
</table>
 
{{-- Unit / Property --}}
<div class="unit-box">
    <strong>Property:</strong> {{ $invoice->unit->property->name }} —
    Unit {{ $invoice->unit->unit_number }},
    {{ $invoice->unit->property->address }}
</div>
 
{{-- Line Items --}}
<table class="lines">
    <thead>
        <tr>
            <th>Description</th>
            <th class="text-right">Amount ({{ $currency }})</th>
        </tr>
    </thead>
    <tbody>
        @if($invoice->rent_amount > 0)
        <tr>
            <td>Rent</td>
            <td class="text-right">{{ number_format($invoice->rent_amount) }}</td>
        </tr>
        @endif
        @if($invoice->water_amount > 0)
        <tr>
            <td>Water</td>
            <td class="text-right">{{ number_format($invoice->water_amount) }}</td>
        </tr>
        @endif
        @if($invoice->garbage_amount > 0)
        <tr>
            <td>Garbage</td>
            <td class="text-right">{{ number_format($invoice->garbage_amount) }}</td>
        </tr>
        @endif
        @if($invoice->other_amount > 0)
        <tr>
            <td>Other Charges</td>
            <td class="text-right">{{ number_format($invoice->other_amount) }}</td>
        </tr>
        @endif
    </tbody>
    <tfoot>
        <tr class="totals-row grand-total">
            <td>Total</td>
            <td class="text-right">{{ $currency }} {{ number_format($invoice->total_amount) }}</td>
        </tr>
        <tr class="totals-row">
            <td style="color:#15803d">Amount Paid</td>
            <td class="text-right" style="color:#15803d">
                {{ $currency }} {{ number_format($invoice->amount_paid) }}
            </td>
        </tr>
        <tr class="balance-row">
            <td>Balance Due</td>
            <td class="text-right">{{ $currency }} {{ number_format($invoice->balance) }}</td>
        </tr>
    </tfoot>
</table>
 
@if($invoiceNotes)
<div class="invoice-notes">
    <strong>Payment Instructions:</strong> {{ $invoiceNotes }}
</div>
@endif
 
@if($invoice->notes)
<div style="font-size:11px;color:#374151;margin-bottom:20px">
    <strong>Notes:</strong> {{ $invoice->notes }}
</div>
@endif
 
<div class="footer">
    {{ $companyName }} — Generated on {{ now()->format('d M Y, h:i A') }}
</div>
 
</body>
</html>
 