@extends('layouts.app')

@section('title', 'New Invoice')
@section('page-title', 'New Invoice')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">Create New Invoice</h2>
                <p class="text-muted mb-0" style="font-size:.8rem">Generate a monthly invoice for a tenant</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <form action="{{ route('invoices.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Tenant / Lease <span class="text-danger">*</span>
                        </label>
                        <select name="lease_id" id="lease_id"
                                class="form-select @error('lease_id') is-invalid @enderror"
                                required onchange="fillRent(this)">
                            <option value="">Select active lease...</option>
                            @foreach($leases as $lease)
                                <option value="{{ $lease->id }}"
                                        data-rent="{{ $lease->monthly_rent }}"
                                        {{ old('lease_id') == $lease->id ? 'selected' : '' }}>
                                    {{ $lease->tenant->user->name }} —
                                    {{ $lease->unit->unit_number }},
                                    {{ $lease->unit->property->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('lease_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Period Start <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="period_start"
                                   class="form-control @error('period_start') is-invalid @enderror"
                                   value="{{ old('period_start') }}" required>
                            @error('period_start')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Period End <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="period_end"
                                   class="form-control @error('period_end') is-invalid @enderror"
                                   value="{{ old('period_end') }}" required>
                            @error('period_end')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Due Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="due_date"
                               class="form-control @error('due_date') is-invalid @enderror"
                               value="{{ old('due_date') }}" required>
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr style="border-color:#f0f0f0;margin:20px 0">
                    <p style="font-size:.8rem;font-weight:700;color:#1a1a2e;margin-bottom:12px">Charges</p>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Rent (KES) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="rent_amount" id="rent_amount"
                                   class="form-control @error('rent_amount') is-invalid @enderror"
                                   value="{{ old('rent_amount') }}"
                                   min="0" required onchange="calcTotal()">
                            @error('rent_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Water (KES)
                            </label>
                            <input type="number" name="water_amount" id="water_amount"
                                   class="form-control" value="{{ old('water_amount', 0) }}"
                                   min="0" onchange="calcTotal()">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Garbage (KES)
                            </label>
                            <input type="number" name="garbage_amount" id="garbage_amount"
                                   class="form-control" value="{{ old('garbage_amount', 0) }}"
                                   min="0" onchange="calcTotal()">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Other (KES)
                            </label>
                            <input type="number" name="other_amount" id="other_amount"
                                   class="form-control" value="{{ old('other_amount', 0) }}"
                                   min="0" onchange="calcTotal()">
                        </div>
                    </div>

                    <div class="p-3 mb-4 d-flex justify-content-between align-items-center"
                         style="background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0">
                        <span style="font-size:.85rem;font-weight:600;color:#15803d">Total Amount</span>
                        <span id="total_display" style="font-size:1.1rem;font-weight:700;color:#15803d">KES 0</span>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Notes
                        </label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Optional notes">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="btn w-100"
                            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:11px;font-size:.9rem;font-weight:600;">
                        <i class="bi bi-receipt me-2"></i>Create Invoice
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function fillRent(select) {
    const option = select.options[select.selectedIndex];
    const rent = option.getAttribute('data-rent') || 0;
    document.getElementById('rent_amount').value = rent;
    calcTotal();
}

function calcTotal() {
    const rent    = parseFloat(document.getElementById('rent_amount').value)    || 0;
    const water   = parseFloat(document.getElementById('water_amount').value)   || 0;
    const garbage = parseFloat(document.getElementById('garbage_amount').value) || 0;
    const other   = parseFloat(document.getElementById('other_amount').value)   || 0;
    const total   = rent + water + garbage + other;
    document.getElementById('total_display').textContent = 'KES ' + total.toLocaleString();
}
</script>
@endpush

@endsection