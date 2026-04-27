@extends('layouts.app')
 
@section('title', 'Units')
@section('page-title', 'Units')
 
@section('content')
 
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">All Units</h2>
        <p class="text-muted mb-0" style="font-size:.82rem">Manage units across all your properties</p>
    </div>
    <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#addUnitModal"
            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
        <i class="bi bi-plus-lg me-1"></i> Add Unit
    </button>
</div>
 
@if($units->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-door-open"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">No units yet</h3>
        <p class="text-muted" style="font-size:.82rem">Click Add Unit to get started</p>
        <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#addUnitModal"
                style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-plus-lg me-1"></i> Add Unit
        </button>
    </div>
@else
    <div class="row g-3 mb-4">
        @foreach($units as $unit)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;overflow:hidden">
 
                {{-- Unit Image --}}
                @if($unit->image_path)
                    <img src="{{ asset('storage/' . $unit->image_path) }}"
                         alt="Unit {{ $unit->unit_number }}"
                         style="width:100%;height:140px;object-fit:cover">
                @else
                    <div style="width:100%;height:140px;background:linear-gradient(135deg,#1a1a2e,#374151);display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-door-open" style="font-size:2rem;color:rgba(255,255,255,.2)"></i>
                    </div>
                @endif
 
                <div class="card-body p-3">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <div style="font-size:.95rem;font-weight:700;color:#1a1a2e">
                                {{ $unit->unit_number }}
                                @if($unit->floor_number > 0)
                                    <span class="text-muted" style="font-size:.75rem;font-weight:400"> — Floor {{ $unit->floor_number }}</span>
                                @endif
                            </div>
                            <div style="font-size:.78rem;color:#6c757d">{{ $unit->property->name }}</div>
                        </div>
                        @if($unit->status === 'occupied')
                            <span class="badge" style="background:#dbeafe;color:#1e40af;border-radius:20px;font-size:.68rem;flex-shrink:0">Occupied</span>
                        @elseif($unit->status === 'vacant')
                            <span class="badge" style="background:#dcfce7;color:#15803d;border-radius:20px;font-size:.68rem;flex-shrink:0">Vacant</span>
                        @else
                            <span class="badge" style="background:#fef3c7;color:#b45309;border-radius:20px;font-size:.68rem;flex-shrink:0">Maintenance</span>
                        @endif
                    </div>
 
                    <div class="d-flex gap-3 mb-3" style="font-size:.78rem;color:#6c757d">
                        <span>{{ ucfirst(str_replace('_', ' ', $unit->type)) }}</span>
                        <span style="color:#1a7a4a;font-weight:600">KES {{ number_format($unit->rent_amount) }}</span>
                        @if($unit->has_water_meter)
                            <span style="color:#1a7a4a"><i class="bi bi-droplet me-1"></i>Metered</span>
                        @endif
                    </div>
 
                    <div class="d-flex gap-2">
                        <a href="{{ route('units.edit', $unit) }}"
                           class="btn btn-sm btn-outline-secondary flex-fill"
                           style="font-size:.75rem;border-radius:6px;padding:5px">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                        <form action="{{ route('units.destroy', $unit) }}" method="POST"
                              onsubmit="return confirm('Delete unit {{ $unit->unit_number }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    style="font-size:.75rem;border-radius:6px;padding:5px 10px">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
 
{{-- Add Unit Modal --}}
<div class="modal fade" id="addUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0">
                <h5 class="modal-title" style="font-size:.95rem;font-weight:700">Add New Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('units.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
 
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Property <span class="text-danger">*</span>
                        </label>
                        <select name="property_id" class="form-select" required>
                            <option value="">Select property...</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}">{{ $property->name }}</option>
                            @endforeach
                        </select>
                    </div>
 
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Unit Number <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="unit_number" class="form-control"
                                   placeholder="e.g. A1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Floor Number
                            </label>
                            <input type="number" name="floor_number" class="form-control"
                                   placeholder="0" min="0">
                        </div>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Unit Type <span class="text-danger">*</span>
                        </label>
                        <select name="type" class="form-select" required>
                            <option value="">Select type...</option>
                            <option value="bedsitter">Bedsitter</option>
                            <option value="single_room">Single Room</option>
                            <option value="one_bedroom">One Bedroom</option>
                            <option value="two_bedroom">Two Bedroom</option>
                            <option value="three_bedroom">Three Bedroom</option>
                            <option value="commercial">Commercial</option>
                        </select>
                    </div>
 
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Rent Amount (KES) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="rent_amount" class="form-control"
                                   placeholder="e.g. 15000" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Deposit Amount (KES)
                            </label>
                            <input type="number" name="deposit_amount" class="form-control"
                                   placeholder="e.g. 15000" min="0">
                        </div>
                    </div>
 
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="has_water_meter" id="has_water_meter_modal" value="1">
                            <label class="form-check-label" for="has_water_meter_modal"
                                   style="font-size:.82rem">Has water meter</label>
                        </div>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Water Meter Number
                        </label>
                        <input type="text" name="water_meter_number" class="form-control"
                               placeholder="e.g. WM-001">
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Notes
                        </label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Optional notes"></textarea>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Unit Photo
                        </label>
                        <input type="file" name="image" class="form-control"
                               accept="image/*" style="font-size:.82rem"
                               onchange="previewModalImage(this)">
                        <small class="text-muted" style="font-size:.72rem">Optional. JPG, PNG or WEBP. Max 2MB.</small>
                        <div id="modal-img-preview-wrapper" style="display:none;margin-top:8px">
                            <img id="modal-img-preview"
                                 style="max-width:100%;max-height:160px;border-radius:8px;border:1px solid #e9ecef">
                        </div>
                    </div>
 
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm"
                            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:6px 20px;font-size:.85rem;font-weight:600;">
                        Add Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 
@push('scripts')
<script>
function previewModalImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('modal-img-preview').src = e.target.result;
            document.getElementById('modal-img-preview-wrapper').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
 
@endsection
 