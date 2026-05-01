@extends('layouts.app')

@section('title', 'Deposits')
@section('page-title', 'Deposits')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">Security Deposits</h2>
        <p class="text-muted mb-0" style="font-size:.82rem">Track tenant security deposits</p>
    </div>
    <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#addDepositModal"
            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
        <i class="bi bi-plus-lg me-1"></i> Record Deposit
    </button>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Total Held</div>
                    <div class="stat-value" style="font-size:1.3rem;color:#1a7a4a">
                        {{ $currency }} {{ number_format($totalHeld) }}
                    </div>
                    <div style="font-size:.72rem;color:#6c757d;margin-top:4px">
                        Current balance held
                    </div>
                </div>
                <div class="stat-icon" style="background:#e8f5ee;color:#1a7a4a">
                    <i class="bi bi-safe"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Total Received</div>
                    <div class="stat-value" style="font-size:1.3rem;color:#15803d">
                        {{ $currency }} {{ number_format($totalReceived) }}
                    </div>
                </div>
                <div class="stat-icon" style="background:#dcfce7;color:#15803d">
                    <i class="bi bi-arrow-down-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Total Refunded</div>
                    <div class="stat-value" style="font-size:1.3rem;color:#b91c1c">
                        {{ $currency }} {{ number_format($totalRefunded) }}
                    </div>
                </div>
                <div class="stat-icon" style="background:#fee2e2;color:#b91c1c">
                    <i class="bi bi-arrow-up-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@if($deposits->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-safe"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">No deposits recorded</h3>
        <p class="text-muted" style="font-size:.82rem">Click Record Deposit to get started</p>
        <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#addDepositModal"
                style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-plus-lg me-1"></i> Record Deposit
        </button>
    </div>
@else
    <div class="card border-0 shadow-sm" style="border-radius:12px">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d">
                    <tr>
                        <th class="px-4 py-3">Tenant</th>
                        <th class="py-3">Unit</th>
                        <th class="py-3">Expected</th>
                        <th class="py-3">Received</th>
                        <th class="py-3">Deducted</th>
                        <th class="py-3">Refunded</th>
                        <th class="py-3">Balance Held</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody style="font-size:.85rem">
                    @foreach($deposits as $deposit)
                    <tr>
                        <td class="px-4 py-3">
                            <div style="font-weight:600;color:#1a1a2e">{{ $deposit->tenant->user->name }}</div>
                            <div style="font-size:.75rem;color:#6c757d">{{ $deposit->date_received ? $deposit->date_received->format('d M Y') : '—' }}</div>
                        </td>
                        <td class="py-3">
                            {{ $deposit->lease->unit->unit_number }}
                            <span class="text-muted" style="font-size:.75rem">
                                — {{ $deposit->lease->unit->property->name }}
                            </span>
                        </td>
                        <td class="py-3">{{ number_format($deposit->amount_expected) }}</td>
                        <td class="py-3" style="color:#15803d;font-weight:600">
                            {{ number_format($deposit->amount_received) }}
                        </td>
                        <td class="py-3" style="color:{{ $deposit->deduction_amount > 0 ? '#b91c1c' : '#6c757d' }}">
                            {{ number_format($deposit->deduction_amount) }}
                        </td>
                        <td class="py-3" style="color:{{ $deposit->refund_amount > 0 ? '#b45309' : '#6c757d' }}">
                            {{ number_format($deposit->refund_amount) }}
                        </td>
                        <td class="py-3" style="font-weight:700;color:{{ $deposit->balanceHeld() > 0 ? '#1a7a4a' : '#6c757d' }}">
                            {{ number_format($deposit->balanceHeld()) }}
                        </td>
                        <td class="py-3">
                            @if($deposit->status === 'received')
                                <span class="badge" style="background:#dcfce7;color:#15803d;border-radius:20px;font-size:.7rem">Received</span>
                            @elseif($deposit->status === 'partial')
                                <span class="badge" style="background:#dbeafe;color:#1e40af;border-radius:20px;font-size:.7rem">Partial</span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#b91c1c;border-radius:20px;font-size:.7rem">Pending</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <div class="d-flex gap-2">
                                <a href="{{ route('deposits.edit', $deposit) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('deposits.destroy', $deposit) }}" method="POST"
                                      onsubmit="return confirm('Delete this deposit record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Add Deposit Modal --}}
<div class="modal fade" id="addDepositModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0">
                <h5 class="modal-title" style="font-size:.95rem;font-weight:700">
                    <i class="bi bi-safe me-2"></i>Record Deposit
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('deposits.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Lease <span class="text-danger">*</span>
                        </label>
                        <select name="lease_id" class="form-select" required>
                            <option value="">Select tenant lease...</option>
                            @foreach(\App\Models\Lease::with('tenant.user', 'unit.property')->where('status', 'active')->get() as $lease)
                                <option value="{{ $lease->id }}">
                                    {{ $lease->tenant->user->name }} —
                                    Unit {{ $lease->unit->unit_number }},
                                    {{ $lease->unit->property->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Amount Expected (KES) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="amount_expected" class="form-control"
                                   placeholder="e.g. 12000" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Amount Received (KES) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="amount_received" class="form-control"
                                   placeholder="e.g. 12000" min="0" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Date Received <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date_received" class="form-control"
                                   value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" class="form-select" required>
                                <option value="received">Received in Full</option>
                                <option value="partial">Partial</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Notes
                        </label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Optional notes"></textarea>
                    </div>

                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm"
                            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:6px 20px;font-size:.85rem;font-weight:600;">
                        <i class="bi bi-check-lg me-1"></i> Record Deposit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection