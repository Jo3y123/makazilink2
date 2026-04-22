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
    <div class="card border-0 shadow-sm" style="border-radius:12px">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d">
                    <tr>
                        <th class="px-4 py-3">Unit</th>
                        <th class="py-3">Property</th>
                        <th class="py-3">Type</th>
                        <th class="py-3">Rent (KES)</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Water Meter</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody style="font-size:.85rem">
                    @foreach($units as $unit)
                    <tr>
                        <td class="px-4 py-3">
                            <span style="font-weight:700;color:#1a1a2e">{{ $unit->unit_number }}</span>
                            @if($unit->floor_number > 0)
                                <span class="text-muted" style="font-size:.75rem"> — Floor {{ $unit->floor_number }}</span>
                            @endif
                        </td>
                        <td class="py-3">{{ $unit->property->name }}</td>
                        <td class="py-3">{{ ucfirst(str_replace('_', ' ', $unit->type)) }}</td>
                        <td class="py-3">{{ number_format($unit->rent_amount) }}</td>
                        <td class="py-3">
                            @if($unit->status === 'occupied')
                                <span class="badge" style="background:#dbeafe;color:#1e40af;border-radius:20px;font-size:.7rem">Occupied</span>
                            @elseif($unit->status === 'vacant')
                                <span class="badge" style="background:#dcfce7;color:#15803d;border-radius:20px;font-size:.7rem">Vacant</span>
                            @else
                                <span class="badge" style="background:#fef3c7;color:#b45309;border-radius:20px;font-size:.7rem">Maintenance</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($unit->has_water_meter)
                                <span style="font-size:.78rem;color:#1a7a4a"><i class="bi bi-check-circle me-1"></i>{{ $unit->water_meter_number }}</span>
                            @else
                                <span class="text-muted" style="font-size:.78rem">—</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <div class="d-flex gap-2">
                                <a href="{{ route('units.edit', $unit) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('units.destroy', $unit) }}" method="POST"
                                      onsubmit="return confirm('Delete unit {{ $unit->unit_number }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
            <form action="{{ route('units.store') }}" method="POST">
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
                                   name="has_water_meter" id="has_water_meter" value="1">
                            <label class="form-check-label" for="has_water_meter"
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

@endsection