@extends('layouts.app')

@section('title', 'Quick Add Tenant')
@section('page-title', 'Quick Add Tenant')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('tenants.index') }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">
                    Quick Add Tenant
                </h2>
                <p class="text-muted mb-0" style="font-size:.8rem">
                    Add a tenant and set up their rental in one step
                </p>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger mb-4" style="border-radius:10px;font-size:.85rem">
            @foreach($errors->all() as $error)
                <div><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</div>
            @endforeach
        </div>
        @endif

        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <form action="{{ route('tenants.quick-store') }}" method="POST">
                    @csrf

                    {{-- Tenant Details --}}
                    <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                        <i class="bi bi-person me-2 text-success"></i>Tenant Details
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ old('name') }}"
                                   placeholder="e.g. John Kamau" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Phone <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone') }}"
                                   placeholder="0712345678" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Email <span style="font-size:.72rem;color:#6c757d">(optional)</span>
                            </label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email') }}"
                                   placeholder="john@email.com">
                            <small style="font-size:.72rem;color:#6c757d">
                                If left blank a temporary email will be generated
                            </small>
                        </div>
                    </div>

                    <div style="border-top:1px solid #f0f0f0;margin-bottom:20px;padding-top:20px">
                        <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                            <i class="bi bi-house me-2 text-primary"></i>Rental Details
                        </p>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                    Unit <span class="text-danger">*</span>
                                </label>
                                <select name="unit_id" class="form-select" required>
                                    <option value="">Select vacant unit...</option>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->unit_number }} — {{ $unit->property->name }}
                                        (KES {{ number_format($unit->rent_amount) }}/mo)
                                    </option>
                                    @endforeach
                                </select>
                                @if($units->isEmpty())
                                <small style="font-size:.72rem;color:#b91c1c">
                                    No vacant units available. Please add a unit first.
                                </small>
                                @endif
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                    Monthly Rent (KES) <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="monthly_rent" class="form-control"
                                       value="{{ old('monthly_rent') }}"
                                       placeholder="e.g. 12000" min="1" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                    Deposit Paid (KES)
                                </label>
                                <input type="number" name="deposit_paid" class="form-control"
                                       value="{{ old('deposit_paid') }}"
                                       placeholder="e.g. 24000" min="0">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                    Move In Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="move_in_date" class="form-control"
                                       value="{{ old('move_in_date', now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                    Notice Period (days)
                                </label>
                                <input type="number" name="notice_days" class="form-control"
                                       value="{{ old('notice_days', 30) }}"
                                       placeholder="30" min="1">
                                <small style="font-size:.72rem;color:#6c757d">
                                    How many days notice to vacate
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Info box --}}
                    <div style="background:#f0fdf4;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:.78rem;color:#15803d">
                        <i class="bi bi-info-circle me-2"></i>
                        This will create a <strong>month to month</strong> rental with no fixed end date.
                        The tenant can log in with their phone number as username and
                        <strong>password</strong> as the default password.
                    </div>

                    <button type="submit" class="btn w-100"
                            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:12px;font-size:.9rem;font-weight:600">
                        <i class="bi bi-person-plus me-2"></i>Add Tenant
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection