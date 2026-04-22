@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')

<div class="mb-4">
    <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">Financial Reports</h2>
    <p class="text-muted mb-0" style="font-size:.82rem">Overview of revenue, occupancy and maintenance</p>
</div>

{{-- Top Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value" style="font-size:1.3rem">KES {{ number_format($totalRevenue) }}</div>
                </div>
                <div class="stat-icon" style="background:#dcfce7;color:#15803d">
                    <i class="bi bi-cash-coin"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">This Month</div>
                    <div class="stat-value" style="font-size:1.3rem">KES {{ number_format($thisMonthRevenue) }}</div>
                </div>
                <div class="stat-icon" style="background:#dbeafe;color:#1e40af">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Outstanding</div>
                    <div class="stat-value" style="font-size:1.3rem;color:#b91c1c">KES {{ number_format($outstandingBalance) }}</div>
                </div>
                <div class="stat-icon" style="background:#fee2e2;color:#b91c1c">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Occupancy Rate</div>
                    <div class="stat-value">{{ $occupancyRate }}%</div>
                </div>
                <div class="stat-icon" style="background:#f3e8ff;color:#7e22ce">
                    <i class="bi bi-building-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">

    {{-- Monthly Revenue Chart --}}
    <div class="col-12 col-md-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:20px">
                    Monthly Revenue — Last 6 Months
                </p>
                @php
                    $maxAmount = max(array_column($monthlyRevenue, 'amount')) ?: 1;
                @endphp
                <div class="d-flex align-items-end gap-2" style="height:180px">
                    @foreach($monthlyRevenue as $month)
                    @php
                        $height = max(4, round(($month['amount'] / $maxAmount) * 160));
                    @endphp
                    <div class="d-flex flex-column align-items-center flex-fill">
                        <div style="font-size:.65rem;color:#6c757d;margin-bottom:4px">
                            KES {{ number_format($month['amount'] / 1000, 0) }}k
                        </div>
                        <div style="width:100%;height:{{ $height }}px;background:#1a7a4a;border-radius:4px 4px 0 0;min-height:4px;transition:height .3s">
                        </div>
                        <div style="font-size:.65rem;color:#6c757d;margin-top:6px;text-align:center">
                            {{ $month['month'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Methods --}}
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:20px">
                    Payment Methods
                </p>
                @if($paymentMethods->isEmpty())
                    <p class="text-muted" style="font-size:.82rem">No payments yet</p>
                @else
                    @foreach($paymentMethods as $method)
                    @php
                        $colors = [
                            'mpesa'         => ['bg' => '#dcfce7', 'color' => '#15803d'],
                            'cash'          => ['bg' => '#fef3c7', 'color' => '#b45309'],
                            'bank_transfer' => ['bg' => '#dbeafe', 'color' => '#1e40af'],
                            'cheque'        => ['bg' => '#f3e8ff', 'color' => '#7e22ce'],
                        ];
                        $c = $colors[$method->payment_method] ?? ['bg' => '#f1f5f9', 'color' => '#64748b'];
                    @endphp
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge" style="background:{{ $c['bg'] }};color:{{ $c['color'] }};border-radius:20px;font-size:.72rem">
                                {{ ucfirst(str_replace('_', ' ', $method->payment_method)) }}
                            </span>
                            <span style="font-size:.78rem;color:#6c757d">{{ $method->count }}x</span>
                        </div>
                        <span style="font-size:.82rem;font-weight:600;color:#1a1a2e">
                            KES {{ number_format($method->total) }}
                        </span>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

</div>

<div class="row g-3 mb-4">

    {{-- Invoice Status --}}
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                    Invoice Status
                </p>
                @foreach([
                    ['Paid',    $invoiceStats['paid'],    '#dcfce7', '#15803d'],
                    ['Partial', $invoiceStats['partial'], '#dbeafe', '#1e40af'],
                    ['Overdue', $invoiceStats['overdue'], '#fee2e2', '#b91c1c'],
                    ['Draft',   $invoiceStats['draft'],   '#f1f5f9', '#64748b'],
                ] as [$label, $count, $bg, $color])
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge" style="background:{{ $bg }};color:{{ $color }};border-radius:20px;font-size:.72rem">
                        {{ $label }}
                    </span>
                    <span style="font-size:.85rem;font-weight:700;color:#1a1a2e">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Occupancy --}}
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                    Occupancy
                </p>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size:.78rem;color:#6c757d">Occupied</span>
                        <span style="font-size:.78rem;font-weight:600">{{ $occupiedUnits }} / {{ $totalUnits }}</span>
                    </div>
                    <div style="height:8px;background:#e9ecef;border-radius:4px">
                        <div style="height:8px;background:#1a7a4a;border-radius:4px;width:{{ $occupancyRate }}%"></div>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="text-center">
                        <div style="font-size:1.3rem;font-weight:700;color:#1a7a4a">{{ $occupiedUnits }}</div>
                        <div style="font-size:.72rem;color:#6c757d">Occupied</div>
                    </div>
                    <div class="text-center">
                        <div style="font-size:1.3rem;font-weight:700;color:#b45309">{{ $vacantUnits }}</div>
                        <div style="font-size:.72rem;color:#6c757d">Vacant</div>
                    </div>
                    <div class="text-center">
                        <div style="font-size:1.3rem;font-weight:700;color:#1a1a2e">{{ $occupancyRate }}%</div>
                        <div style="font-size:.72rem;color:#6c757d">Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Maintenance --}}
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                    Maintenance
                </p>
                @foreach([
                    ['Open',        $maintenanceStats['open'],        '#fee2e2', '#b91c1c'],
                    ['In Progress', $maintenanceStats['in_progress'], '#fef3c7', '#b45309'],
                    ['Resolved',    $maintenanceStats['resolved'],    '#dcfce7', '#15803d'],
                ] as [$label, $count, $bg, $color])
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge" style="background:{{ $bg }};color:{{ $color }};border-radius:20px;font-size:.72rem">
                        {{ $label }}
                    </span>
                    <span style="font-size:.85rem;font-weight:700;color:#1a1a2e">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- Recent Payments --}}
<div class="card border-0 shadow-sm" style="border-radius:12px">
    <div class="card-body p-4">
        <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
            Recent Payments
        </p>
        @if($recentPayments->isEmpty())
            <p class="text-muted" style="font-size:.82rem">No payments yet</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead style="font-size:.72rem;text-transform:uppercase;color:#6c757d">
                        <tr>
                            <th style="border:none">Receipt</th>
                            <th style="border:none">Tenant</th>
                            <th style="border:none">Unit</th>
                            <th style="border:none">Method</th>
                            <th style="border:none">Date</th>
                            <th style="border:none;text-align:right">Amount</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:.82rem">
                        @foreach($recentPayments as $payment)
                        <tr>
                            <td style="border-color:#f0f0f0">
                                <a href="{{ route('payments.show', $payment) }}"
                                   style="color:#1a7a4a;font-weight:600">
                                    {{ $payment->receipt_number }}
                                </a>
                            </td>
                            <td style="border-color:#f0f0f0">{{ $payment->tenant->user->name }}</td>
                            <td style="border-color:#f0f0f0">{{ $payment->unit->unit_number }}</td>
                            <td style="border-color:#f0f0f0">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            <td style="border-color:#f0f0f0">{{ $payment->payment_date->format('d M Y') }}</td>
                            <td style="border-color:#f0f0f0;text-align:right;font-weight:600;color:#15803d">
                                KES {{ number_format($payment->amount) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection