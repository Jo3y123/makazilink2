@extends('layouts.app')
 
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
 
@section('content')
 
@php
    $alertDays = (int) \App\Models\Setting::get('lease_alert_days', 30);
    $currency  = \App\Models\Setting::get('currency', 'KES');
 
    $expiringLeases = \App\Models\Lease::with('tenant.user', 'unit.property')
        ->where('status', 'active')
        ->whereNotNull('end_date')
        ->whereDate('end_date', '<=', now()->addDays($alertDays))
        ->get();
 
    $arrearsInvoices = \App\Models\Invoice::with('tenant.user', 'unit.property')
    ->whereIn('status', ['draft', 'sent', 'overdue', 'partial'])
    ->where('balance', '>', 0)
    ->orderByDesc('balance')
    ->get();
@endphp
 
@if($expiringLeases->count() > 0)
<div class="alert mb-4" style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:16px 20px">
    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="bi bi-exclamation-triangle-fill" style="color:#b45309;font-size:1rem"></i>
        <span style="font-weight:700;color:#b45309;font-size:.9rem">
            {{ $expiringLeases->count() }} lease{{ $expiringLeases->count() > 1 ? 's' : '' }} expiring within {{ $alertDays }} days
        </span>
    </div>
    @foreach($expiringLeases as $lease)
    <div class="d-flex align-items-center justify-content-between py-1"
         style="border-top:1px solid #fde68a;margin-top:6px">
        <span style="font-size:.82rem;color:#92400e">
            <strong>{{ $lease->tenant->user->name }}</strong> —
            {{ $lease->unit->unit_number }}, {{ $lease->unit->property->name }}
        </span>
        <span style="font-size:.78rem;color:#b45309;font-weight:600">
            Expires {{ $lease->end_date->format('d M Y') }}
            ({{ $lease->days_until_expiry }} days)
        </span>
    </div>
    @endforeach
</div>
@endif
 
{{-- Header with date filter --}}
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
            {{ auth()->user()->name }} 👋
        </h2>
        <p class="text-muted mb-0" style="font-size:.82rem">
            {{ now()->format('l, d F Y') }} &mdash; Here is your property overview
        </p>
    </div>
 
    @hasrole(['admin', 'accountant'])
    <form method="GET" action="{{ route('dashboard') }}"
          class="d-flex align-items-center gap-2 flex-wrap">
        <div class="d-flex align-items-center gap-2"
             style="background:#fff;border:1px solid #e9ecef;border-radius:10px;padding:8px 14px">
            <i class="bi bi-calendar3" style="color:#6c757d;font-size:.85rem"></i>
            <input type="date" name="date_from" value="{{ $dateFrom }}"
                   style="font-size:.82rem;font-family:'Plus Jakarta Sans',sans-serif;outline:none;width:120px;color:#1a1a2e;border:none;background:transparent">
            <span style="color:#6c757d;font-size:.82rem">to</span>
            <input type="date" name="date_to" value="{{ $dateTo }}"
                   style="font-size:.82rem;font-family:'Plus Jakarta Sans',sans-serif;outline:none;width:120px;color:#1a1a2e;border:none;background:transparent">
        </div>
        <button type="submit" class="btn btn-sm"
                style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 16px;font-size:.82rem;font-weight:600">
            <i class="bi bi-funnel me-1"></i>Filter
        </button>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary"
           style="border-radius:8px;padding:8px 12px;font-size:.82rem">
            Reset
        </a>
    </form>
    @endhasrole
</div>
 
@if(auth()->user()->hasRole(['admin', 'agent', 'accountant', 'caretaker']))
<div class="row g-3 mb-4">
 
    @hasrole(['admin', 'agent'])
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Properties</div>
                    <div class="stat-value">{{ $stats['total_properties'] }}</div>
                </div>
                <div class="stat-icon" style="background:#e8f5ee;color:#1a7a4a">
                    <i class="bi bi-building"></i>
                </div>
            </div>
        </div>
    </div>
    @endhasrole
 
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Occupied</div>
                    <div class="stat-value">{{ $stats['occupied_units'] }}</div>
                </div>
                <div class="stat-icon" style="background:#dbeafe;color:#1e40af">
                    <i class="bi bi-door-closed"></i>
                </div>
            </div>
        </div>
    </div>
 
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Vacant</div>
                    <div class="stat-value">{{ $stats['vacant_units'] }}</div>
                </div>
                <div class="stat-icon" style="background:#fef3c7;color:#b45309">
                    <i class="bi bi-door-open"></i>
                </div>
            </div>
        </div>
    </div>
 
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Tenants</div>
                    <div class="stat-value">{{ $stats['total_tenants'] }}</div>
                </div>
                <div class="stat-icon" style="background:#f3e8ff;color:#7e22ce">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
 
    @hasrole(['admin', 'accountant'])
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">
                        Revenue
                        <span style="font-size:.65rem;color:#9ca3af;font-weight:400;display:block">
                            {{ \Carbon\Carbon::parse($dateFrom)->format('d M') }} — {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
                        </span>
                    </div>
                    <div class="stat-value" style="font-size:1.3rem">
                        {{ $currency }} {{ number_format($stats['monthly_revenue']) }}
                    </div>
                </div>
                <div class="stat-icon" style="background:#dcfce7;color:#15803d">
                    <i class="bi bi-cash-coin"></i>
                </div>
            </div>
        </div>
    </div>
 
    <div class="col-6 col-md-3" style="cursor:pointer" onclick="document.getElementById('arrearsModal').style.display='flex'">
        <div class="stat-card" style="transition:box-shadow .2s"
             onmouseover="this.style.boxShadow='0 4px 20px rgba(185,28,28,.15)'"
             onmouseout="this.style.boxShadow=''">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">
                        Arrears
                        <span style="font-size:.65rem;color:#1a7a4a;font-weight:600"> ↗ click</span>
                    </div>
                    <div class="stat-value">{{ $stats['pending_payments'] }}</div>
                </div>
                <div class="stat-icon" style="background:#fee2e2;color:#b91c1c">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
            </div>
        </div>
    </div>
    @endhasrole
 
    @hasrole(['admin', 'caretaker'])
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Maintenance</div>
                    <div class="stat-value">{{ $stats['open_maintenance'] }}</div>
                </div>
                <div class="stat-icon" style="background:#fff7ed;color:#c2410c">
                    <i class="bi bi-tools"></i>
                </div>
            </div>
        </div>
    </div>
 
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Expiring Soon</div>
                    <div class="stat-value">{{ $stats['expiring_leases'] }}</div>
                </div>
                <div class="stat-icon" style="background:#fce7f3;color:#9d174d">
                    <i class="bi bi-calendar-x"></i>
                </div>
            </div>
        </div>
    </div>
    @endhasrole
 
</div>
@endif
 
@hasrole(['admin', 'accountant'])
<div class="row g-3 mb-4">
    <div class="col-12 col-md-8">
        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                    Monthly Revenue — Last 6 Months
                </p>
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                    Unit Occupancy
                </p>
                <canvas id="occupancyChart" height="180"></canvas>
            </div>
        </div>
    </div>
</div>
 
<div class="card border-0 shadow-sm" style="border-radius:12px">
    <div class="card-body p-4">
        <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
            Recent Payments
        </p>
        @php
            $recentPayments = \App\Models\Payment::with('tenant.user', 'unit')
                ->where('status', 'confirmed')
                ->latest()
                ->take(5)
                ->get();
        @endphp
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
                            <td style="border-color:#f0f0f0;text-align:right;font-weight:600;color:#15803d">
                                {{ $currency }} {{ number_format($payment->amount) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endhasrole
 
@hasrole(['caretaker'])
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                    <i class="bi bi-tools me-2 text-warning"></i>Open Maintenance Requests
                </p>
                @php
                    $openRequests = \App\Models\MaintenanceRequest::with('unit.property')
                        ->whereIn('status', ['open', 'in_progress'])
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp
                @if($openRequests->isEmpty())
                    <p class="text-muted" style="font-size:.82rem">No open maintenance requests.</p>
                @else
                    @foreach($openRequests as $req)
                    <div class="d-flex align-items-center justify-content-between py-2"
                         style="border-bottom:1px solid #f0f0f0">
                        <div>
                            <div style="font-size:.82rem;font-weight:600;color:#1a1a2e">{{ $req->title }}</div>
                            <div style="font-size:.75rem;color:#6c757d">
                                Unit {{ $req->unit->unit_number }} — {{ $req->unit->property->name }}
                            </div>
                        </div>
                        @if($req->priority === 'urgent')
                            <span class="badge" style="background:#fee2e2;color:#b91c1c;border-radius:20px;font-size:.7rem">Urgent</span>
                        @elseif($req->priority === 'high')
                            <span class="badge" style="background:#fef3c7;color:#b45309;border-radius:20px;font-size:.7rem">High</span>
                        @else
                            <span class="badge" style="background:#dbeafe;color:#1e40af;border-radius:20px;font-size:.7rem">{{ ucfirst($req->priority) }}</span>
                        @endif
                    </div>
                    @endforeach
                    <div class="mt-3">
                        <a href="{{ route('maintenance.index') }}" style="font-size:.8rem;color:#1a7a4a">
                            View all maintenance requests →
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                    <i class="bi bi-droplet me-2 text-primary"></i>Recent Water Readings
                </p>
                @php
                    $recentReadings = \App\Models\WaterReading::with('unit.property')
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp
                @if($recentReadings->isEmpty())
                    <p class="text-muted" style="font-size:.82rem">No water readings recorded yet.</p>
                @else
                    @foreach($recentReadings as $reading)
                    <div class="d-flex align-items-center justify-content-between py-2"
                         style="border-bottom:1px solid #f0f0f0">
                        <div>
                            <div style="font-size:.82rem;font-weight:600;color:#1a1a2e">
                                Unit {{ $reading->unit->unit_number }}
                            </div>
                            <div style="font-size:.75rem;color:#6c757d">
                                {{ $reading->billing_period }} — {{ $reading->units_consumed }} units
                            </div>
                        </div>
                        <div style="font-size:.82rem;font-weight:600;color:#15803d">
                            KES {{ number_format($reading->amount_charged) }}
                        </div>
                    </div>
                    @endforeach
                    <div class="mt-3">
                        <a href="{{ route('water.index') }}" style="font-size:.8rem;color:#1a7a4a">
                            View all water readings →
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endhasrole
 
{{-- Arrears Modal --}}
@hasrole(['admin', 'accountant'])
<div id="arrearsModal"
     style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.5);z-index:9000;align-items:center;justify-content:center"
     onclick="if(event.target===this) this.style.display='none'">
    <div style="background:#fff;border-radius:16px;width:90%;max-width:640px;max-height:80vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2)">
        <div style="padding:20px 24px;border-bottom:1px solid #f0f0f0;display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
            <div>
                <div style="font-size:1rem;font-weight:700;color:#1a1a2e">
                    <i class="bi bi-exclamation-circle me-2" style="color:#b91c1c"></i>Arrears
                </div>
                <div style="font-size:.78rem;color:#6c757d;margin-top:2px">
                    {{ $arrearsInvoices->count() }} unpaid invoice(s) —
                    Total: {{ $currency }} {{ number_format($arrearsInvoices->sum('balance')) }}
                </div>
            </div>
            <button onclick="document.getElementById('arrearsModal').style.display='none'"
                    style="background:none;border:none;font-size:1.2rem;color:#6c757d;cursor:pointer;padding:0">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div style="overflow-y:auto;flex:1">
            @if($arrearsInvoices->isEmpty())
                <div style="text-align:center;padding:40px;color:#9ca3af;font-size:.85rem">
                    <i class="bi bi-check-circle fs-3 d-block mb-2" style="color:#15803d"></i>
                    No arrears. All tenants are up to date!
                </div>
            @else
                @foreach($arrearsInvoices as $invoice)
                <div style="padding:14px 24px;border-bottom:1px solid #f8fafc;display:flex;align-items:center;justify-content:space-between;gap:12px">
                    <div style="flex:1;min-width:0">
                        <div style="font-size:.85rem;font-weight:700;color:#1a1a2e">
                            {{ $invoice->tenant->user->name }}
                        </div>
                        <div style="font-size:.75rem;color:#6c757d;margin-top:2px">
                            {{ $invoice->invoice_number }} —
                            Unit {{ $invoice->unit->unit_number }},
                            {{ $invoice->unit->property->name }} —
                            Due {{ $invoice->due_date->format('d M Y') }}
                        </div>
                    </div>
                    <div style="text-align:right;flex-shrink:0">
                        <div style="font-size:.9rem;font-weight:700;color:#b91c1c">
                            {{ $currency }} {{ number_format($invoice->balance) }}
                        </div>
                        @if($invoice->status === 'overdue')
                            <span style="background:#fee2e2;color:#b91c1c;padding:2px 8px;border-radius:20px;font-size:.68rem;font-weight:600">Overdue</span>
                        @else
                            <span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:20px;font-size:.68rem;font-weight:600">Partial</span>
                        @endif
                    </div>
                </div>
                @endforeach
            @endif
        </div>
        <div style="padding:16px 24px;border-top:1px solid #f0f0f0;flex-shrink:0;display:flex;gap:8px;justify-content:flex-end">
            <a href="{{ route('invoices.index') }}" class="btn btn-sm"
               style="background:#1a7a4a;color:#fff;border-radius:8px;font-size:.82rem;padding:7px 16px">
                View All Invoices
            </a>
            <button onclick="document.getElementById('arrearsModal').style.display='none'"
                    class="btn btn-sm btn-outline-secondary"
                    style="border-radius:8px;font-size:.82rem;padding:7px 16px">
                Close
            </button>
        </div>
    </div>
</div>
@endhasrole
 
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@php
    $months = [];
    $amounts = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = now()->subMonths($i);
        $months[] = $month->format('M Y');
        $amounts[] = \App\Models\Payment::whereMonth('payment_date', $month->month)
            ->whereYear('payment_date', $month->year)
            ->where('status', 'confirmed')
            ->sum('amount');
    }
@endphp
const revenueCtx = document.getElementById('revenueChart');
if (revenueCtx) {
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Revenue ({{ $currency }})',
                data: {!! json_encode($amounts) !!},
                backgroundColor: '#1a7a4a',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '{{ $currency }} ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}
 
const occupancyCtx = document.getElementById('occupancyChart');
if (occupancyCtx) {
    new Chart(occupancyCtx, {
        type: 'doughnut',
        data: {
            labels: ['Occupied', 'Vacant'],
            datasets: [{
                data: [{{ $stats['occupied_units'] }}, {{ $stats['vacant_units'] }}],
                backgroundColor: ['#1a7a4a', '#e9ecef'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 } }
                }
            }
        }
    });
}
</script>
@endpush
 
@endsection
 