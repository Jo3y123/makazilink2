@extends('layouts.app')

@section('title', 'Edit Unit')
@section('page-title', 'Edit Unit')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('units.index') }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">Edit Unit</h2>
                <p class="text-muted mb-0" style="font-size:.8rem">{{ $unit->unit_number }} — {{ $unit->property->name }}</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <form action="{{ route('units.update', $unit) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Property <span class="text-danger">*</span>
                        </label>
                        <select name="property_id" class="form-select" required>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}"
                                    {{ $unit->property_id == $property->id ? 'selected' : '' }}>
                                    {{ $property->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Unit Number <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="unit_number" class="form-control"
                                   value="{{ old('unit_number', $unit->unit_number) }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Floor Number
                            </label>
                            <input type="number" name="floor_number" class="form-control"
                                   value="{{ old('floor_number', $unit->floor_number) }}" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Unit Type <span class="text-danger">*</span>
                        </label>
                        <select name="type" class="form-select" required>
                            <option value="bedsitter"     {{ $unit->type === 'bedsitter'     ? 'selected' : '' }}>Bedsitter</option>
                            <option value="single_room"   {{ $unit->type === 'single_room'   ? 'selected' : '' }}>Single Room</option>
                            <option value="one_bedroom"   {{ $unit->type === 'one_bedroom'   ? 'selected' : '' }}>One Bedroom</option>
                            <option value="two_bedroom"   {{ $unit->type === 'two_bedroom'   ? 'selected' : '' }}>Two Bedroom</option>
                            <option value="three_bedroom" {{ $unit->type === 'three_bedroom' ? 'selected' : '' }}>Three Bedroom</option>
                            <option value="commercial"    {{ $unit->type === 'commercial'    ? 'selected' : '' }}>Commercial</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Rent Amount (KES) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="rent_amount" class="form-control"
                                   value="{{ old('rent_amount', $unit->rent_amount) }}" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Deposit Amount (KES)
                            </label>
                            <input type="number" name="deposit_amount" class="form-control"
                                   value="{{ old('deposit_amount', $unit->deposit_amount) }}" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Status
                        </label>
                        <select name="status" class="form-select">
                            <option value="vacant"            {{ $unit->status === 'vacant'            ? 'selected' : '' }}>Vacant</option>
                            <option value="occupied"          {{ $unit->status === 'occupied'          ? 'selected' : '' }}>Occupied</option>
                            <option value="under_maintenance" {{ $unit->status === 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="has_water_meter" id="has_water_meter" value="1"
                                   {{ $unit->has_water_meter ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_water_meter"
                                   style="font-size:.82rem">Has water meter</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Water Meter Number
                        </label>
                        <input type="text" name="water_meter_number" class="form-control"
                               value="{{ old('water_meter_number', $unit->water_meter_number) }}"
                               placeholder="e.g. WM-001">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Notes
                        </label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Optional notes">{{ old('notes', $unit->notes) }}</textarea>
                    </div>

                    {{-- Unit Image --}}
                    <div class="mb-4">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Unit Photo
                        </label>
                        @if($unit->image_path)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $unit->image_path) }}"
                                     alt="Unit {{ $unit->unit_number }}"
                                     style="max-width:100%;max-height:180px;border-radius:8px;border:1px solid #e9ecef">
                                <div style="font-size:.72rem;color:#6c757d;margin-top:4px">
                                    Current photo — upload a new one to replace it
                                </div>
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control"
                               accept="image/*"
                               style="font-size:.82rem"
                               onchange="previewImage(this)">
                        <small class="text-muted" style="font-size:.72rem">
                            Optional. JPG, PNG or WEBP. Max 2MB.
                        </small>
                        <div id="img-preview-wrapper" style="display:none;margin-top:10px">
                            <img id="img-preview"
                                 style="max-width:100%;max-height:200px;border-radius:8px;border:1px solid #e9ecef">
                        </div>
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

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('img-preview').src = e.target.result;
            document.getElementById('img-preview-wrapper').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush

@endsection