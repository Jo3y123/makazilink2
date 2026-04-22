@extends('layouts.app')

@section('title', 'Edit Property')
@section('page-title', 'Edit Property')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('properties.index') }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">Edit Property</h2>
                <p class="text-muted mb-0" style="font-size:.8rem">{{ $property->name }}</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <form action="{{ route('properties.update', $property) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Property Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $property->name) }}"
                               placeholder="e.g. Sunset Apartments">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Property Type <span class="text-danger">*</span>
                        </label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror">
                            <option value="">Select type...</option>
                            <option value="apartment"    {{ old('type', $property->type) === 'apartment'    ? 'selected' : '' }}>Apartment Block</option>
                            <option value="maisonette"   {{ old('type', $property->type) === 'maisonette'   ? 'selected' : '' }}>Maisonette</option>
                            <option value="bedsitter"    {{ old('type', $property->type) === 'bedsitter'    ? 'selected' : '' }}>Bedsitter</option>
                            <option value="single_room"  {{ old('type', $property->type) === 'single_room'  ? 'selected' : '' }}>Single Room</option>
                            <option value="double_room"  {{ old('type', $property->type) === 'double_room'  ? 'selected' : '' }}>Double Room</option>
                            <option value="commercial"   {{ old('type', $property->type) === 'commercial'   ? 'selected' : '' }}>Commercial</option>
                            <option value="land"         {{ old('type', $property->type) === 'land'         ? 'selected' : '' }}>Land</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Address <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="address"
                               class="form-control @error('address') is-invalid @enderror"
                               value="{{ old('address', $property->address) }}"
                               placeholder="e.g. Mombasa Road, Nairobi">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Town
                            </label>
                            <input type="text" name="town"
                                   class="form-control"
                                   value="{{ old('town', $property->town) }}"
                                   placeholder="e.g. Nairobi">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                County
                            </label>
                            <input type="text" name="county"
                                   class="form-control"
                                   value="{{ old('county', $property->county) }}"
                                   placeholder="e.g. Nairobi County">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Description
                        </label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Optional notes about this property">{{ old('description', $property->description) }}</textarea>
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