@extends('layouts.app')

@section('title', 'Shared Water Billing')
@section('page-title', 'Shared Water Billing')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('properties.index') }}" class="btn btn-sm btn-outline-secondary"
       style="border-radius:8px">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">
            Shared Water Billing
        </h2>
        <p class="text-muted mb-0" style="font-size:.8rem">
            {{ $property->name }} — {{ $units->count() }} occupied unit(s)
        </p>
    </div>
</div>

@if($units->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-droplet"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">No occupied units</h3>
        <p class="text-muted" style="font-size:.82rem">
            There are no occupied units with active leases in this property.
        </p>
    </div>
@else

<div class="row justify-content-center">
    <div class="col-12 col-md-8">

        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">

                <form action="{{ route('properties.water.apply', $property) }}" method="POST"
                      id="waterForm">
                    @csrf

                    {{-- Total Water Bill --}}
                    <div class="mb-4">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Total Water Bill (KES) <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="total_water_bill" id="totalBill"
                               class="form-control" placeholder="e.g. 6000"
                               min="1" required
                               oninput="calculateSplit()">
                        <small class="text-muted" style="font-size:.72rem">
                            Enter the total water bill for the whole property
                        </small>
                    </div>

                    {{-- Split Method --}}
                    <div class="mb-4">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Split Method <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="split_method"
                                       id="equalSplit" value="equal" checked
                                       onchange="toggleSplitMethod()">
                                <label class="form-check-label" for="equalSplit"
                                       style="font-size:.85rem">
                                    Equal Split
                                    <span style="font-size:.72rem;color:#6c757d">— divide equally among selected units</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="split_method"
                                       id="customSplit" value="custom"
                                       onchange="toggleSplitMethod()">
                                <label class="form-check-label" for="customSplit"
                                       style="font-size:.85rem">
                                    Custom
                                    <span style="font-size:.72rem;color:#6c757d">— enter amount per unit</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Units Selection --}}
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label mb-0" style="font-size:.8rem;font-weight:600;color:#374151">
                                Select Units <span class="text-danger">*</span>
                            </label>
                            <button type="button" onclick="selectAll()"
                                    class="btn btn-sm btn-outline-secondary"
                                    style="border-radius:6px;font-size:.75rem;padding:3px 12px">
                                <i class="bi bi-check-all me-1"></i>Select All
                            </button>
                        </div>

                        <div style="border:1.5px solid #e5e7eb;border-radius:10px;overflow:hidden">
                            @foreach($units as $unit)
                            <div class="d-flex align-items-center justify-content-between p-3"
                                 style="border-bottom:1px solid #f0f0f0">
                                <div class="d-flex align-items-center gap-3">
                                    <input class="form-check-input unit-checkbox" type="checkbox"
                                           name="unit_ids[]"
                                           value="{{ $unit->id }}"
                                           id="unit_{{ $unit->id }}"
                                           checked
                                           onchange="calculateSplit()">
                                    <label for="unit_{{ $unit->id }}" style="cursor:pointer;margin:0">
                                        <div style="font-size:.85rem;font-weight:600;color:#1a1a2e">
                                            Unit {{ $unit->unit_number }}
                                        </div>
                                        <div style="font-size:.75rem;color:#6c757d">
                                            {{ $unit->activeLease->tenant->user->name }}
                                        </div>
                                    </label>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    {{-- Equal split amount display --}}
                                    <span class="equal-amount badge"
                                          id="equalAmount_{{ $unit->id }}"
                                          style="background:#e8f5ee;color:#1a7a4a;font-size:.75rem;padding:4px 10px;border-radius:20px">
                                        KES 0
                                    </span>
                                    {{-- Custom amount input --}}
                                    <input type="number"
                                           name="custom_amounts[{{ $unit->id }}]"
                                           class="custom-amount form-control form-control-sm"
                                           placeholder="Amount"
                                           min="0"
                                           style="display:none;width:100px;font-size:.82rem;border-radius:6px">
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <small class="text-muted" style="font-size:.72rem">
                            <span id="selectedCount">{{ $units->count() }}</span> unit(s) selected
                        </small>
                    </div>

                    {{-- Summary --}}
                    <div class="p-3 mb-4" id="equalSummary"
                         style="background:#f0fdf4;border-radius:8px;border:1px solid #86efac">
                        <div style="font-size:.82rem;color:#15803d;font-weight:600">
                            <i class="bi bi-calculator me-2"></i>
                            Each unit will be charged:
                            <span id="perUnitAmount" style="font-size:1rem">KES 0</span>
                        </div>
                        <div style="font-size:.75rem;color:#6c757d;margin-top:4px">
                            Total bill: KES <span id="totalDisplay">0</span> ÷
                            <span id="unitCountDisplay">{{ $units->count() }}</span> units
                        </div>
                    </div>

                    <div class="alert alert-info mb-4" style="font-size:.82rem;border-radius:8px">
                        <i class="bi bi-info-circle me-2"></i>
                        The water charge will be added to each tenant's current unpaid invoice.
                        If a tenant has no unpaid invoice a new one will be created automatically.
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn"
                                style="background:#1a7a4a;color:#fff;border-radius:8px;padding:10px 28px;font-size:.9rem;font-weight:600;">
                            <i class="bi bi-droplet me-2"></i>Apply Water Charges
                        </button>
                        <a href="{{ route('properties.index') }}"
                           class="btn btn-outline-secondary"
                           style="border-radius:8px;padding:10px 20px;font-size:.9rem">
                            Cancel
                        </a>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

@endif

@push('scripts')
<script>
const unitCount = {{ $units->count() }};

function selectAll() {
    const checkboxes = document.querySelectorAll('.unit-checkbox');
    const allChecked = Array.from(checkboxes).every(c => c.checked);
    checkboxes.forEach(c => c.checked = !allChecked);
    const btn = event.target.closest('button');
    btn.innerHTML = allChecked
        ? '<i class="bi bi-check-all me-1"></i>Select All'
        : '<i class="bi bi-x me-1"></i>Deselect All';
    calculateSplit();
}

function calculateSplit() {
    const total     = parseFloat(document.getElementById('totalBill').value) || 0;
    const checked   = document.querySelectorAll('.unit-checkbox:checked');
    const count     = checked.length;
    const perUnit   = count > 0 ? Math.round((total / count) * 100) / 100 : 0;

    document.getElementById('selectedCount').textContent  = count;
    document.getElementById('perUnitAmount').textContent  = 'KES ' + perUnit.toLocaleString();
    document.getElementById('totalDisplay').textContent   = total.toLocaleString();
    document.getElementById('unitCountDisplay').textContent = count;

    // Update each unit's equal amount badge
    document.querySelectorAll('.unit-checkbox').forEach(cb => {
        const badge = document.getElementById('equalAmount_' + cb.value);
        if (badge) {
            badge.textContent = cb.checked ? 'KES ' + perUnit.toLocaleString() : 'KES 0';
            badge.style.opacity = cb.checked ? '1' : '0.4';
        }
    });
}

function toggleSplitMethod() {
    const isCustom = document.getElementById('customSplit').checked;

    document.querySelectorAll('.equal-amount').forEach(el => {
        el.style.display = isCustom ? 'none' : 'inline';
    });
    document.querySelectorAll('.custom-amount').forEach(el => {
        el.style.display = isCustom ? 'block' : 'none';
    });
    document.getElementById('equalSummary').style.display = isCustom ? 'none' : 'block';
}

// Initialize
calculateSplit();
</script>
@endpush

@endsection