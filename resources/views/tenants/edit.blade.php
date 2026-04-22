@extends('layouts.app')

@section('title', 'Edit Tenant')
@section('page-title', 'Edit Tenant')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('tenants.index') }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">Edit Tenant</h2>
                <p class="text-muted mb-0" style="font-size:.8rem">{{ $tenant->user->name }}</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <form action="{{ route('tenants.update', $tenant) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <p style="font-size:.8rem;font-weight:700;color:#1a1a2e;margin-bottom:12px">
                        Personal Details
                    </p>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $tenant->user->name) }}"
                               placeholder="e.g. John Kamau">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Email
                            </label>
                            <input type="email" class="form-control"
                                   value="{{ $tenant->user->email }}" disabled>
                            <small class="text-muted" style="font-size:.72rem">Email cannot be changed</small>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Phone <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $tenant->user->phone) }}"
                                   placeholder="0712345678">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                ID Number
                            </label>
                            <input type="text" name="id_number"
                                   class="form-control"
                                   value="{{ old('id_number', $tenant->id_number) }}"
                                   placeholder="e.g. 12345678">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Occupation
                            </label>
                            <input type="text" name="occupation"
                                   class="form-control"
                                   value="{{ old('occupation', $tenant->occupation) }}"
                                   placeholder="e.g. Teacher">
                        </div>
                    </div>

                    <hr style="border-color:#f0f0f0;margin:20px 0">

                    <p style="font-size:.8rem;font-weight:700;color:#1a1a2e;margin-bottom:12px">
                        Emergency Contact
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Contact Name
                            </label>
                            <input type="text" name="emergency_contact_name"
                                   class="form-control"
                                   value="{{ old('emergency_contact_name', $tenant->emergency_contact_name) }}"
                                   placeholder="e.g. Jane Kamau">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Contact Phone
                            </label>
                            <input type="text" name="emergency_contact_phone"
                                   class="form-control"
                                   value="{{ old('emergency_contact_phone', $tenant->emergency_contact_phone) }}"
                                   placeholder="0712345678">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Notes
                        </label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Optional notes">{{ old('notes', $tenant->notes) }}</textarea>
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