@extends('layouts.app')

@section('title', 'Salaries')
@section('page-title', 'Salaries')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">Salary Records</h2>
        <p class="text-muted mb-0" style="font-size:.82rem">Track staff salary payments</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('export.salaries') }}" class="btn btn-sm btn-outline-secondary"
           style="border-radius:8px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
        <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#addSalaryModal"
                style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-plus-lg me-1"></i> Record Salary
        </button>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">This Month</div>
                    <div class="stat-value" style="font-size:1.3rem;color:#b91c1c">
                        {{ $currency }} {{ number_format($totalThisMonth) }}
                    </div>
                    <div style="font-size:.72rem;color:#6c757d;margin-top:4px">
                        {{ now()->setMonth($month)->format('F') }} {{ $year }}
                    </div>
                </div>
                <div class="stat-icon" style="background:#fee2e2;color:#b91c1c">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Total All Time</div>
                    <div class="stat-value" style="font-size:1.3rem;color:#b91c1c">
                        {{ $currency }} {{ number_format($totalAllTime) }}
                    </div>
                </div>
                <div class="stat-icon" style="background:#fee2e2;color:#b91c1c">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Records This Month</div>
                    <div class="stat-value">{{ $salaries->count() }}</div>
                </div>
                <div class="stat-icon" style="background:#dbeafe;color:#1e40af">
                    <i class="bi bi-list-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Month/Year Filter --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius:12px">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('salaries.index') }}" class="d-flex gap-2 align-items-center flex-wrap">
            <select name="month" class="form-select form-select-sm" style="width:auto;border-radius:8px">
                @foreach($months as $m)
                    <option value="{{ $m['value'] }}" {{ $m['value'] == $month ? 'selected' : '' }}>
                        {{ $m['label'] }}
                    </option>
                @endforeach
            </select>
            <select name="year" class="form-select form-select-sm" style="width:auto;border-radius:8px">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-outline-secondary" style="border-radius:8px">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <a href="{{ route('salaries.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px">
                Reset
            </a>
        </form>
    </div>
</div>

@if($salaries->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-people"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">No salary records</h3>
        <p class="text-muted" style="font-size:.82rem">
            No salaries recorded for {{ now()->setMonth($month)->format('F') }} {{ $year }}
        </p>
        <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#addSalaryModal"
                style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-plus-lg me-1"></i> Record Salary
        </button>
    </div>
@else
    <div class="card border-0 shadow-sm" style="border-radius:12px">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d">
                    <tr>
                        <th class="px-4 py-3">Staff Name</th>
                        <th class="py-3">Role</th>
                        <th class="py-3">Amount ({{ $currency }})</th>
                        <th class="py-3">Payment Method</th>
                        <th class="py-3">Reference</th>
                        <th class="py-3">Date</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody style="font-size:.85rem">
                    @foreach($salaries as $salary)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px;height:32px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;color:#b91c1c;flex-shrink:0">
                                    {{ strtoupper(substr($salary->staff_name, 0, 2)) }}
                                </div>
                                <span style="font-weight:600;color:#1a1a2e">{{ $salary->staff_name }}</span>
                            </div>
                        </td>
                        <td class="py-3">{{ $salary->role ?? '—' }}</td>
                        <td class="py-3">
                            <span style="font-weight:700;color:#b91c1c">{{ number_format($salary->amount) }}</span>
                        </td>
                        <td class="py-3">
                            @if($salary->payment_method === 'mpesa')
                                <span class="badge" style="background:#dcfce7;color:#15803d;border-radius:20px;font-size:.7rem">M-Pesa</span>
                            @elseif($salary->payment_method === 'bank_transfer')
                                <span class="badge" style="background:#dbeafe;color:#1e40af;border-radius:20px;font-size:.7rem">Bank</span>
                            @else
                                <span class="badge" style="background:#fef3c7;color:#b45309;border-radius:20px;font-size:.7rem">Cash</span>
                            @endif
                        </td>
                        <td class="py-3">{{ $salary->reference ?? '—' }}</td>
                        <td class="py-3">{{ $salary->payment_date->format('d M Y') }}</td>
                        <td class="py-3">
                            <form action="{{ route('salaries.destroy', $salary) }}" method="POST"
                                  onsubmit="return confirm('Delete this salary record?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="background:#f8fafc">
                    <tr>
                        <td colspan="2" class="px-4 py-3" style="font-weight:700;font-size:.85rem">Total</td>
                        <td class="py-3" style="font-weight:700;color:#b91c1c;font-size:.85rem">
                            {{ number_format($totalThisMonth) }}
                        </td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endif

{{-- Add Salary Modal --}}
<div class="modal fade" id="addSalaryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0">
                <h5 class="modal-title" style="font-size:.95rem;font-weight:700">
                    <i class="bi bi-people me-2"></i>Record Salary Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('salaries.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Staff Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="staff_name" class="form-control"
                                   placeholder="e.g. John Kamau" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Role
                            </label>
                            <input type="text" name="role" class="form-control"
                                   placeholder="e.g. Caretaker, Security, Cleaner">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Amount ({{ $currency }}) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="amount" class="form-control"
                                   placeholder="e.g. 15000" min="1" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Payment Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="payment_date" class="form-control"
                                   value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Payment Method <span class="text-danger">*</span>
                            </label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mpesa">M-Pesa</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Reference / Receipt No
                            </label>
                            <input type="text" name="reference" class="form-control"
                                   placeholder="e.g. M-Pesa code or receipt no">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Notes
                            </label>
                            <textarea name="notes" class="form-control" rows="2"
                                      placeholder="Optional notes"></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm"
                            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:6px 20px;font-size:.85rem;font-weight:600;">
                        <i class="bi bi-check-lg me-1"></i> Record Salary
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection