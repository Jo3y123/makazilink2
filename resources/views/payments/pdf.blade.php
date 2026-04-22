<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $payment->receipt_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1a1a2e;
            padding: 40px;
        }
        .header {
            border-bottom: 2px solid #1a7a4a;
            padding-bottom: 20px;
            margin-bottom: 30px;
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
        .receipt-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a7a4a;
            margin-top: 16px;
        }
        .receipt-number {
            font-size: 11px;
            color: #6c757d;
        }
        .details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .details-box { width: 48%; }
        .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6c757d;
            margin-bottom: 3px;
        }
        .value {
            font-size: 12px;
            font-weight: 600;
            color: #1a1a2e;
        }
        .sub {
            font-size: 10px;
            color: #6c757d;
            margin-top: 2px;
        }
        .amount-box {
            background: #0f2d1e;
            color: #fff;
            text-align: center;
            padding: 24px;
            border-radius: 8px;
            margin: 24px 0;
        }
        .amount-label {
            font-size: 10px;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }
        .amount-value {
            font-size: 28px;
            font-weight: 700;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 11px;
        }
        .info-label { color: #6c757d; }
        .info-value { font-weight: 600; color: #1a1a2e; }
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
@endphp

    <div class="header">
        <div class="brand-name">{{ $companyName }}</div>
        <div class="brand-sub">Rental Management System</div>
        <div class="brand-contact">
            @if($companyPhone) {{ $companyPhone }}<br> @endif
            @if($companyEmail) {{ $companyEmail }}<br> @endif
            @if($companyAddress) {{ $companyAddress }} @endif
        </div>
        <div class="receipt-title">PAYMENT RECEIPT</div>
        <div class="receipt-number">{{ $payment->receipt_number }}</div>
    </div>

    <div class="details-row">
        <div class="details-box">
            <div class="label">Received From</div>
            <div class="value">{{ $payment->tenant->user->name }}</div>
            <div class="sub">{{ $payment->tenant->user->phone }}</div>
            <div class="sub">{{ $payment->tenant->user->email }}</div>
        </div>
        <div class="details-box" style="text-align:right">
            <div class="label">Payment Date</div>
            <div class="value">{{ $payment->payment_date->format('d M Y') }}</div>
        </div>
    </div>

    <div class="details-row">
        <div class="details-box">
            <div class="label">Unit</div>
            <div class="value">{{ $payment->unit->unit_number }}</div>
            <div class="sub">{{ $payment->unit->property->name }}</div>
            <div class="sub">{{ $payment->unit->property->address }}</div>
        </div>
        <div class="details-box" style="text-align:right">
            <div class="label">Payment Method</div>
            <div class="value">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</div>
            @if($payment->mpesa_transaction_id)
                <div class="sub">{{ $payment->mpesa_transaction_id }}</div>
            @endif
            @if($payment->reference_number)
                <div class="sub">Ref: {{ $payment->reference_number }}</div>
            @endif
        </div>
    </div>

    @if($payment->invoice)
    <div class="info-row">
        <span class="info-label">Invoice</span>
        <span class="info-value">{{ $payment->invoice->invoice_number }}</span>
    </div>
    @endif

    <div class="amount-box">
        <div class="amount-label">Amount Paid</div>
        <div class="amount-value">{{ $currency }} {{ number_format($payment->amount) }}</div>
    </div>

    @if($payment->notes)
    <div class="info-row">
        <span class="info-label">Notes</span>
        <span class="info-value">{{ $payment->notes }}</span>
    </div>
    @endif

    <div class="info-row">
        <span class="info-label">Recorded By</span>
        <span class="info-value">{{ $payment->recordedBy->name }}</span>
    </div>

    <div class="footer">
        {{ $companyName }} — Generated on {{ now()->format('d M Y, h:i A') }}
    </div>

</body>
</html>