@extends('layouts.app')

@section('title', 'Edit Deposit')
@section('page-title', 'Edit Deposit')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('deposits.index') }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">Edit Deposit</h2>
                <p class="text-muted mb-0" style="font-size:.8rem">
                    {{ $deposit->tenant->user->name }} —
                    Unit {{ $deposit->lease->unit->unit_number }},
                    {{ $deposit->lease->unit->property->name }}
                </p>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <form action="{{ route('deposits.update', $deposit) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Deposit Receipt --}}
                    <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                        <i class="bi bi-arrow-down-circle me-2 text-success"></i>Deposit Received
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Amount Received (KES) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="amount_received" class="form-control"
                                   value="{{ old('amount_received', $deposit->amount_received) }}"
                                   min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Date Received
                            </label>
                            <input type="date" name="date_received" class="form-control"
                                   value="{{ old('date_received', $deposit->date_received?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" class="form-select" required>
                                <option value="received"  {{ $deposit->status === 'received'  ? 'selected' : '' }}>Received in Full</option>
                                <option value="partial"   {{ $deposit->status === 'partial'   ? 'selected' : '' }}>Partial</option>
                                <option value="pending"   {{ $deposit->status === 'pending'   ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                    </div>

                    <div style="border-top:1px solid #f0f0f0;margin-bottom:20px;padding-top:20px">
                        <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                            <i class="bi bi-scissors me-2 text-danger"></i>Deductions (when tenant vacates)
                        </p>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                    Deduction Amount (KES)
                                </label>
                                <input type="number" name="deduction_amount" class="form-control"
                                       value="{{ old('deduction_amount', $deposit->deduction_amount) }}"
                                       min="0" oninput="updateBalance()">
                            </div>
                            <div class="col-6">
                                <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                    Deduction Reason
                                </label>
                                <input type="text" name="deduction_reason" class="form-control"
                                       value="{{ old('deduction_reason', $deposit->deduction_reason) }}"
                                       placeholder="e.g. Damaged wall, unpaid rent">
                            </div>
                        </div>
                    </div>

                    <div style="border-top:1px solid #f0f0f0;margin-bottom:20px;padding-top:20px">
                        <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                            <i class="bi bi-arrow-up-circle me-2 text-warning"></i>Refund to Tenant
                        </p>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                    Refund Amount (KES)
                                </label>
                                <input type="number" name="refund_amount" class="form-control"
                                       value="{{ old('refund_amount', $deposit->refund_amount) }}"
                                       min="0" oninput="updateBalance()">
                            </div>
                            <div class="col-6">
                                <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                    Refund Date
                                </label>
                                <input type="date" name="refund_date" class="form-control"
                                       value="{{ old('refund_date', $deposit->refund_date?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                    Refund Method
                                </label>
                                <select name="refund_method" class="form-select">
                                    <option value="">Select...</option>
                                    <option value="cash"          {{ $deposit->refund_method === 'cash'          ? 'selected' : '' }}>Cash</option>
                                    <option value="mpesa"         {{ $deposit->refund_method === 'mpesa'         ? 'selected' : '' }}>M-Pesa</option>
                                    <option value="bank_transfer" {{ $deposit->refund_method === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                    Refund Reference
                                </label>
                                <input type="text" name="refund_reference" class="form-control"
                                       value="{{ old('refund_reference', $deposit->refund_reference) }}"
                                       placeholder="e.g. M-Pesa code or receipt">
                            </div>
                        </div>
                    </div>

                    {{-- Balance Summary --}}
                    <div class="p-3 mb-4" style="background:#f0fdf4;border-radius:8px;border:1px solid #86efac">
                        <div style="font-size:.82rem;color:#15803d;font-weight:600">
                            Balance Currently Held:
                            <span id="balanceDisplay">
                                KES {{ number_format($deposit->balanceHeld()) }}
                            </span>
                        </div>
                        <div style="font-size:.75rem;color:#6c757d;margin-top:4px">
                            Received − Deductions − Refunded
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Notes
                        </label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Optional notes">{{ old('notes', $deposit->notes) }}</textarea>
                    </div>

                    <button type="submit" class="btn w-100"
                            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:11px;font-size:.9rem;font-weight:600">
                        <i class="bi bi-check-lg me-2"></i>Save Changes
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
const received   = {{ $deposit->amount_received }};

function updateBalance() {
    const deduction = parseFloat(document.querySelector('[name="deduction_amount"]')?.value) || 0;
    const refund    = parseFloat(document.querySelector('[name="refund_amount"]')?.value) || 0;
    const balance   = received - deduction - refund;
    document.getElementById('balanceDisplay').textContent = 'KES ' + balance.toLocaleString();
}
</script>
@endpush

@endsection