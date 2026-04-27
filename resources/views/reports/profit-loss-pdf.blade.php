<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profit & Loss — {{ $year }}</title>
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
        .summary-row {
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
        .summary-box .s-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; color: #6c757d; margin-bottom: 4px; }
        .summary-box .s-value { font-size: 14px; font-weight: 700; }
        .bg-green { background: #dcfce7; } .c-green { color: #15803d; }
        .bg-red   { background: #fee2e2; } .c-red   { color: #b91c1c; }
        .bg-blue  { background: #dbeafe; } .c-blue  { color: #1e40af; }
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
        tbody td { padding: 8px 10px; border-bottom: 1px solid #f0f0f0; font-size: 11px; }
        tfoot td { padding: 8px 10px; font-weight: 700; font-size: 11px; background: #f8fafc; border-top: 2px solid #e9ecef; }
        .text-right { text-align: right; }
        .c-profit { color: #15803d; }
        .c-loss   { color: #b91c1c; }
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
        <h1>PROFIT & LOSS</h1>
        <div class="sub">Year: {{ $year }}</div>
        <div class="sub">Generated: {{ now()->format('d M Y') }}</div>
    </div>
</div>

<div class="summary-row">
    <div class="summary-box bg-green">
        <div class="s-label">Total Income</div>
        <div class="s-value c-green">{{ $currency }} {{ number_format($totalIncome) }}</div>
    </div>
    <div class="summary-box bg-red">
        <div class="s-label">Total Expenses</div>
        <div class="s-value c-red">{{ $currency }} {{ number_format($totalExpenses) }}</div>
    </div>
    <div class="summary-box {{ $totalProfit >= 0 ? 'bg-green' : 'bg-red' }}">
        <div class="s-label">Net Profit</div>
        <div class="s-value {{ $totalProfit >= 0 ? 'c-green' : 'c-red' }}">
            {{ $currency }} {{ number_format($totalProfit) }}
        </div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Month</th>
            <th class="text-right">Income ({{ $currency }})</th>
            <th class="text-right">Expenses ({{ $currency }})</th>
            <th class="text-right">Net Profit ({{ $currency }})</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($months as $month)
        <tr>
            <td><strong>{{ $month['month'] }}</strong></td>
            <td class="text-right c-profit">{{ number_format($month['income']) }}</td>
            <td class="text-right {{ $month['expenses'] > 0 ? 'c-loss' : '' }}">{{ number_format($month['expenses']) }}</td>
            <td class="text-right {{ $month['profit'] >= 0 ? 'c-profit' : 'c-loss' }}">
                <strong>{{ number_format($month['profit']) }}</strong>
            </td>
            <td>
                @if($month['income'] == 0 && $month['expenses'] == 0)
                    No Activity
                @elseif($month['profit'] >= 0)
                    Profit
                @else
                    Loss
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>Total</td>
            <td class="text-right c-profit">{{ number_format($totalIncome) }}</td>
            <td class="text-right c-loss">{{ number_format($totalExpenses) }}</td>
            <td class="text-right {{ $totalProfit >= 0 ? 'c-profit' : 'c-loss' }}">
                {{ number_format($totalProfit) }}
            </td>
            <td>{{ $totalProfit >= 0 ? 'Profitable' : 'Loss' }}</td>
        </tr>
    </tfoot>
</table>

<div class="footer">
    {{ $companyName }} — Profit & Loss Report {{ $year }} — Generated on {{ now()->format('d M Y, h:i A') }}
</div>

</body>
</html>