@extends('layouts.app')

@section('title', 'Record Payment')
@section('page-title', 'Record Payment')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">Record Payment</h2>
                <p class="text-muted mb-0" style="font-size:.8rem">Record a rent payment and generate a receipt</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <form action="{{ route('payments.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Invoice (optional)
                        </label>
                        <select name="invoice_id" id="invoice_id"
                                class="form-select"
                                onchange="fillFromInvoice(this)">
                            <option value="">Select invoice (or leave blank for direct payment)...</option>
                            @foreach($invoices as $invoice)
                                <option value="{{ $invoice->id }}"
                                        data-tenant="{{ $invoice->tenant_id }}"
                                        data-unit="{{ $invoice->unit_id }}"
                                        data-balance="{{ $invoice->balance }}"
                                        {{ request('invoice_id') == $invoice->id ? 'selected' : '' }}>
                                    {{ $invoice->invoice_number }} —
                                    {{ $invoice->tenant->user->name }} —
                                    Balance: KES {{ number_format($invoice->balance) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Tenant <span class="text-danger">*</span>
                        </label>
                        <select name="tenant_id" id="tenant_id"
                                class="form-select @error('tenant_id') is-invalid @enderror"
                                required onchange="fillUnit(this)">
                            <option value="">Select tenant...</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}"
                                        data-unit="{{ $tenant->activeLease?->unit_id }}"
                                        {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                    {{ $tenant->user->name }} — {{ $tenant->user->phone }}
                                </option>
                            @endforeach
                        </select>
                        @error('tenant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <input type="hidden" name="unit_id" id="unit_id" value="{{ old('unit_id') }}">

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Amount (KES) <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="amount" id="amount"
                               class="form-control @error('amount') is-invalid @enderror"
                               value="{{ old('amount') }}"
                               placeholder="e.g. 15000" min="1" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Payment Method <span class="text-danger">*</span>
                        </label>
                        <select name="payment_method" id="payment_method"
                                class="form-select @error('payment_method') is-invalid @enderror"
                                required onchange="toggleMpesa(this)">
                            <option value="mpesa"         {{ old('payment_method') === 'mpesa'         ? 'selected' : '' }}>M-Pesa</option>
                            <option value="cash"          {{ old('payment_method') === 'cash'          ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="cheque"        {{ old('payment_method') === 'cheque'        ? 'selected' : '' }}>Cheque</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="mpesa_field">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            M-Pesa Transaction ID
                        </label>
                        <input type="text" name="mpesa_transaction_id"
                               class="form-control"
                               value="{{ old('mpesa_transaction_id') }}"
                               placeholder="e.g. QHJ7K8L9MN">
                    </div>

                    <div class="mb-3" id="reference_field" style="display:none">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Reference Number
                        </label>
                        <input type="text" name="reference_number"
                               class="form-control"
                               value="{{ old('reference_number') }}"
                               placeholder="Cheque or transfer reference">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Payment Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="payment_date"
                               class="form-control @error('payment_date') is-invalid @enderror"
                               value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                        <i class="bi bi-cash-coin me-2"></i>Record Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleMpesa(select) {
    const mpesa     = document.getElementById('mpesa_field');
    const reference = document.getElementById('reference_field');
    if (select.value === 'mpesa') {
        mpesa.style.display     = 'block';
        reference.style.display = 'none';
    } else {
        mpesa.style.display     = 'none';
        reference.style.display = 'block';
    }
}

function fillFromInvoice(select) {
    const option = select.options[select.selectedIndex];
    const tenantId = option.getAttribute('data-tenant');
    const unitId   = option.getAttribute('data-unit');
    const balance  = option.getAttribute('data-balance');
    if (tenantId) {
        document.getElementById('tenant_id').value = tenantId;
        document.getElementById('unit_id').value   = unitId;
        document.getElementById('amount').value    = balance;
    }
}

function fillUnit(select) {
    const option = select.options[select.selectedIndex];
    const unitId = option.getAttribute('data-unit');
    if (unitId) {
        document.getElementById('unit_id').value = unitId;
    }
}
</script>
@endpush

@endsection