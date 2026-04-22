@extends('layouts.app')

@section('title', 'Edit Lease')
@section('page-title', 'Edit Lease')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('leases.index') }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">Edit Lease</h2>
                <p class="text-muted mb-0" style="font-size:.8rem">
                    {{ $lease->tenant->user->name }} — {{ $lease->unit->unit_number }}
                </p>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <form action="{{ route('leases.update', $lease) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Tenant
                        </label>
                        <input type="text" class="form-control" disabled
                               value="{{ $lease->tenant->user->name }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Unit
                        </label>
                        <input type="text" class="form-control" disabled
                               value="{{ $lease->unit->unit_number }} — {{ $lease->unit->property->name }}">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Start Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="start_date"
                                   class="form-control @error('start_date') is-invalid @enderror"
                                   value="{{ old('start_date', $lease->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                End Date
                            </label>
                            <input type="date" name="end_date"
                                   class="form-control @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date', $lease->end_date?->format('Y-m-d')) }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Monthly Rent (KES) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="monthly_rent"
                                   class="form-control @error('monthly_rent') is-invalid @enderror"
                                   value="{{ old('monthly_rent', $lease->monthly_rent) }}"
                                   min="0" required>
                            @error('monthly_rent')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Deposit Paid (KES)
                            </label>
                            <input type="number" name="deposit_paid"
                                   class="form-control"
                                   value="{{ old('deposit_paid', $lease->deposit_paid) }}"
                                   min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select name="status" class="form-select" required>
                            <option value="active"     {{ $lease->status === 'active'     ? 'selected' : '' }}>Active</option>
                            <option value="expired"    {{ $lease->status === 'expired'    ? 'selected' : '' }}>Expired</option>
                            <option value="terminated" {{ $lease->status === 'terminated' ? 'selected' : '' }}>Terminated</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Notice Period (days)
                        </label>
                        <input type="number" name="notice_days"
                               class="form-control"
                               value="{{ old('notice_days', $lease->notice_days) }}"
                               min="0">
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Lease Terms
                        </label>
                        <textarea name="terms" class="form-control" rows="3"
                                  placeholder="Optional lease terms">{{ old('terms', $lease->terms) }}</textarea>
                    </div>

                    <button type="submit" class="btn w-100"
                            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:11px;font-size:.9rem;font-weight:600;">
                        <i class="bi bi-check-lg me-2"></i>Save Changes
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection