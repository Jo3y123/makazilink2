@extends('layouts.app')

@section('title', 'New Maintenance Request')
@section('page-title', 'New Maintenance Request')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('maintenance.index') }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">New Maintenance Request</h2>
                <p class="text-muted mb-0" style="font-size:.8rem">Log a repair or maintenance issue</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <form action="{{ route('maintenance.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Unit <span class="text-danger">*</span>
                        </label>
                        <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                            <option value="">Select unit...</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->unit_number }} — {{ $unit->property->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Reported By (Tenant)
                        </label>
                        <select name="tenant_id" class="form-select">
                            <option value="">Select tenant (optional)...</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                    {{ $tenant->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}"
                               placeholder="e.g. Leaking pipe in bathroom">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Description <span class="text-danger">*</span>
                        </label>
                        <textarea name="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Describe the issue in detail">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="">Select...</option>
                                <option value="plumbing"    {{ old('category') === 'plumbing'    ? 'selected' : '' }}>Plumbing</option>
                                <option value="electrical"  {{ old('category') === 'electrical'  ? 'selected' : '' }}>Electrical</option>
                                <option value="structural"  {{ old('category') === 'structural'  ? 'selected' : '' }}>Structural</option>
                                <option value="cleaning"    {{ old('category') === 'cleaning'    ? 'selected' : '' }}>Cleaning</option>
                                <option value="pest_control" {{ old('category') === 'pest_control' ? 'selected' : '' }}>Pest Control</option>
                                <option value="other"       {{ old('category') === 'other'       ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Priority <span class="text-danger">*</span>
                            </label>
                            <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                <option value="low"    {{ old('priority') === 'low'    ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high"   {{ old('priority') === 'high'   ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Photos (optional)
                        </label>
                        <input type="file" name="photos[]" class="form-control"
                               multiple accept="image/*">
                        <small class="text-muted" style="font-size:.72rem">
                            You can upload multiple photos. Max 2MB each.
                        </small>
                    </div>

                    <button type="submit" class="btn w-100"
                            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:11px;font-size:.9rem;font-weight:600;">
                        <i class="bi bi-tools me-2"></i>Submit Request
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection