@extends('layouts.app')

@section('title', 'Edit Invoice ' . $invoice->invoice_number)
@section('page-title', 'Edit Invoice')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('invoices.show', $invoice) }}"
               class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">
                    Edit Invoice
                </h2>
                <p class="text-muted mb-0" style="font-size:.8rem">
                    {{ $invoice->invoice_number }} —
                    {{ $invoice->tenant->user->name }}
                </p>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <form action="{{ route('invoices.update', $invoice) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Tenant & Unit Info --}}
                    <div class="p-3 mb-4" style="background:#f8fafc;border-radius:8px">
                        <div style="font-size:.78rem;color:#6c757d">
                            <i class="bi bi-person me-1"></i>
                            <strong>{{ $invoice->tenant->user->name }}</strong> —
                            Unit {{ $invoice->unit->unit_number }},
                            {{ $invoice->unit->property->name }}
                        </div>
                    </div>

                    {{-- Amounts --}}
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Rent (KES) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="rent_amount" class="form-control"
                                   value="{{ old('rent_amount', $invoice->rent_amount) }}"
                                   min="0" step="0.01" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Water (KES)
                            </label>
                            <input type="number" name="water_amount" class="form-control"
                                   value="{{ old('water_amount', $invoice->water_amount) }}"
                                   min="0" step="0.01">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Garbage (KES)
                            </label>
                            <input type="number" name="garbage_amount" class="form-control"
                                   value="{{ old('garbage_amount', $invoice->garbage_amount) }}"
                                   min="0" step="0.01">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Other (KES)
                            </label>
                            <input type="number" name="other_amount" class="form-control"
                                   value="{{ old('other_amount', $invoice->other_amount) }}"
                                   min="0" step="0.01">
                        </div>
                    </div>

                    {{-- Live Total --}}
                    <div class="p-3 mb-4" style="background:#f0fdf4;border-radius:8px;border:1px solid #86efac">
                        <div style="font-size:.82rem;color:#15803d">
                            New Total: <strong id="liveTotal">KES {{ number_format($invoice->total_amount) }}</strong>
                        </div>
                        <div style="font-size:.75rem;color:#6c757d;margin-top:2px">
                            Amount already paid: KES {{ number_format($invoice->amount_paid) }}
                        </div>
                    </div>

                    {{-- Period --}}
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Period Start <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="period_start" class="form-control"
                                   value="{{ old('period_start', $invoice->period_start->format('Y-m-d')) }}"
                                   required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Period End <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="period_end" class="form-control"
                                   value="{{ old('period_end', $invoice->period_end->format('Y-m-d')) }}"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Due Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="due_date" class="form-control"
                               value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}"
                               required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Notes
                        </label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Optional notes">{{ old('notes', $invoice->notes) }}</textarea>
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
// Live total calculation
const fields = ['rent_amount', 'water_amount', 'garbage_amount', 'other_amount'];
fields.forEach(f => {
    const el = document.querySelector(`[name="${f}"]`);
    if (el) el.addEventListener('input', updateTotal);
});

function updateTotal() {
    let total = 0;
    fields.forEach(f => {
        const val = parseFloat(document.querySelector(`[name="${f}"]`)?.value) || 0;
        total += val;
    });
    document.getElementById('liveTotal').textContent = 'KES ' + total.toLocaleString();
}
</script>
@endpush

@endsection