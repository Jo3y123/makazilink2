@extends('layouts.app')

@section('title', 'Profit & Loss')
@section('page-title', 'Profit & Loss')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">Profit & Loss Report</h2>
        <p class="text-muted mb-0" style="font-size:.82rem">Income vs expenses breakdown</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <form method="GET" action="{{ route('reports.profit-loss') }}" class="d-flex gap-2">
            <select name="year" class="form-select form-select-sm" style="width:auto;border-radius:8px">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-outline-secondary" style="border-radius:8px">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
        </form>
        <a href="{{ route('reports.profit-loss.pdf') }}?year={{ $year }}"
           class="btn btn-sm btn-outline-secondary" style="border-radius:8px">
            <i class="bi bi-file-pdf me-1"></i>PDF
        </a>
        <a href="{{ route('export.profit-loss') }}?year={{ $year }}"
           class="btn btn-sm btn-outline-secondary" style="border-radius:8px">
            <i class="bi bi-file-earmark-excel me-1"></i>Excel
        </a>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Total Income</div>
                    <div class="stat-value" style="font-size:1.3rem;color:#15803d">
                        {{ $currency }} {{ number_format($totalIncome) }}
                    </div>
                    <div style="font-size:.72rem;color:#6c757d;margin-top:4px">{{ $year }}</div>
                </div>
                <div class="stat-icon" style="background:#dcfce7;color:#15803d">
                    <i class="bi bi-arrow-up-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Total Expenses</div>
                    <div class="stat-value" style="font-size:1.3rem;color:#b91c1c">
                        {{ $currency }} {{ number_format($totalExpenses) }}
                    </div>
                    <div style="font-size:.72rem;color:#6c757d;margin-top:4px">Maintenance costs</div>
                </div>
                <div class="stat-icon" style="background:#fee2e2;color:#b91c1c">
                    <i class="bi bi-arrow-down-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Net Profit</div>
                    <div class="stat-value" style="font-size:1.3rem;color:{{ $totalProfit >= 0 ? '#15803d' : '#b91c1c' }}">
                        {{ $currency }} {{ number_format($totalProfit) }}
                    </div>
                    <div style="font-size:.72rem;color:#6c757d;margin-top:4px">
                        {{ $totalProfit >= 0 ? '✅ Profitable' : '⚠️ Loss' }}
                    </div>
                </div>
                <div class="stat-icon" style="background:{{ $totalProfit >= 0 ? '#dcfce7' : '#fee2e2' }};color:{{ $totalProfit >= 0 ? '#15803d' : '#b91c1c' }}">
                    <i class="bi bi-graph-up"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius:12px">
    <div class="card-body p-4">
        <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
            Monthly Breakdown — {{ $year }}
        </p>
        <canvas id="plChart" height="80"></canvas>
    </div>
</div>

{{-- Monthly Table --}}
<div class="card border-0 shadow-sm" style="border-radius:12px">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#f8fafc;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d">
                <tr>
                        <th class="px-4 py-3">Month</th>
                        <th class="py-3">Income ({{ $currency }})</th>
                        <th class="py-3">Maintenance ({{ $currency }})</th>
                        <th class="py-3">Salaries ({{ $currency }})</th>
                        <th class="py-3">Total Expenses ({{ $currency }})</th>
                        <th class="py-3">Net Profit ({{ $currency }})</th>
                        <th class="py-3">Status</th>
                    </tr>
            </thead>
            <tbody style="font-size:.85rem">
                    @foreach($months as $month)
                    <tr>
                        <td class="px-4 py-3" style="font-weight:600;color:#1a1a2e">{{ $month['month'] }}</td>
                        <td class="py-3" style="color:#15803d;font-weight:600">
                            {{ number_format($month['income']) }}
                        </td>
                        <td class="py-3" style="color:{{ $month['maintenance'] > 0 ? '#b91c1c' : '#6c757d' }}">
                            {{ number_format($month['maintenance']) }}
                        </td>
                        <td class="py-3" style="color:{{ $month['salaries'] > 0 ? '#b91c1c' : '#6c757d' }}">
                            {{ number_format($month['salaries']) }}
                        </td>
                        <td class="py-3" style="color:{{ $month['expenses'] > 0 ? '#b91c1c' : '#6c757d' }};font-weight:600">
                            {{ number_format($month['expenses']) }}
                        </td>
                        <td class="py-3" style="font-weight:700;color:{{ $month['profit'] >= 0 ? '#15803d' : '#b91c1c' }}">
                            {{ number_format($month['profit']) }}
                        </td>
                        <td class="py-3">
                            @if($month['income'] == 0 && $month['expenses'] == 0)
                                <span class="badge" style="background:#f1f5f9;color:#64748b;border-radius:20px;font-size:.7rem">No Activity</span>
                            @elseif($month['profit'] >= 0)
                                <span class="badge" style="background:#dcfce7;color:#15803d;border-radius:20px;font-size:.7rem">Profit</span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#b91c1c;border-radius:20px;font-size:.7rem">Loss</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            <tfoot style="background:#f8fafc">
                        <tr style="font-size:.85rem;font-weight:700">
                            <td class="px-4 py-3" style="color:#1a1a2e">Total</td>
                            <td class="py-3" style="color:#15803d">{{ number_format($totalIncome) }}</td>
                            <td class="py-3" style="color:#b91c1c">{{ number_format(collect($months)->sum('maintenance')) }}</td>
                            <td class="py-3" style="color:#b91c1c">{{ number_format(collect($months)->sum('salaries')) }}</td>
                            <td class="py-3" style="color:#b91c1c">{{ number_format($totalExpenses) }}</td>
                            <td class="py-3" style="color:{{ $totalProfit >= 0 ? '#15803d' : '#b91c1c' }}">
                                {{ number_format($totalProfit) }}
                            </td>
                            <td class="py-3"></td>
                        </tr>
                    </tfoot>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const plCtx = document.getElementById('plChart');
if (plCtx) {
    new Chart(plCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(collect($months)->pluck('month')) !!},
            datasets: [
                {
                    label: 'Income',
                    data: {!! json_encode(collect($months)->pluck('income')) !!},
                    backgroundColor: '#1a7a4a',
                    borderRadius: 4,
                },
                {
                    label: 'Expenses',
                    data: {!! json_encode(collect($months)->pluck('expenses')) !!},
                    backgroundColor: '#fee2e2',
                    borderRadius: 4,
                },
                {
                    label: 'Net Profit',
                    data: {!! json_encode(collect($months)->pluck('profit')) !!},
                    backgroundColor: '#dbeafe',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 } }
                }
            },
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
</script>
@endpush

@endsection