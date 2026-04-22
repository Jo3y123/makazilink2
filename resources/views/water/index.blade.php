@extends('layouts.app')

@section('title', 'Water Readings')
@section('page-title', 'Water Readings')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">Water Meter Readings</h2>
        <p class="text-muted mb-0" style="font-size:.82rem">Record and track monthly water consumption</p>
    </div>
    <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#addReadingModal"
            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
        <i class="bi bi-plus-lg me-1"></i> Record Reading
    </button>
</div>

@if($units->isEmpty())
    <div class="alert alert-warning" style="border-radius:10px;font-size:.85rem">
        <i class="bi bi-exclamation-triangle me-2"></i>
        No units have water meters set up. Go to
        <a href="{{ route('units.index') }}">Units</a>
        and enable water meters on the relevant units first.
    </div>
@endif

@if($readings->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-droplet"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">No readings yet</h3>
        <p class="text-muted" style="font-size:.82rem">Click Record Reading to add the first water reading</p>
    </div>
@else
    <div class="card border-0 shadow-sm" style="border-radius:12px">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d">
                    <tr>
                        <th class="px-4 py-3">Unit</th>
                        <th class="py-3">Billing Period</th>
                        <th class="py-3">Previous</th>
                        <th class="py-3">Current</th>
                        <th class="py-3">Consumed</th>
                        <th class="py-3">Rate</th>
                        <th class="py-3">Amount (KES)</th>
                        <th class="py-3">Date</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody style="font-size:.85rem">
                    @foreach($readings as $reading)
                    <tr>
                        <td class="px-4 py-3">
                            <span style="font-weight:600">{{ $reading->unit->unit_number }}</span>
                            <span class="text-muted" style="font-size:.75rem"> — {{ $reading->unit->property->name }}</span>
                        </td>
                        <td class="py-3">{{ $reading->billing_period }}</td>
                        <td class="py-3">{{ $reading->previous_reading }}</td>
                        <td class="py-3">{{ $reading->current_reading }}</td>
                        <td class="py-3">
                            <span style="font-weight:600;color:#1e40af">{{ $reading->units_consumed }}</span>
                        </td>
                        <td class="py-3">{{ number_format($reading->rate_per_unit) }}</td>
                        <td class="py-3">
                            <span style="font-weight:700;color:#15803d">{{ number_format($reading->amount_charged) }}</span>
                        </td>
                        <td class="py-3">{{ $reading->reading_date->format('d M Y') }}</td>
                        <td class="py-3">
                            <form action="{{ route('water.destroy', $reading) }}" method="POST"
                                  onsubmit="return confirm('Delete this reading?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Add Reading Modal --}}
<div class="modal fade" id="addReadingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0">
                <h5 class="modal-title" style="font-size:.95rem;font-weight:700">Record Water Reading</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('water.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Unit <span class="text-danger">*</span>
                        </label>
                        <select name="unit_id" class="form-select" required>
                            <option value="">Select unit with water meter...</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">
                                    {{ $unit->unit_number }} — {{ $unit->property->name }}
                                    @if($unit->water_meter_number)
                                        ({{ $unit->water_meter_number }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Billing Period <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="billing_period" class="form-control"
                               placeholder="e.g. April 2026" required
                               value="{{ now()->format('F Y') }}">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Previous Reading <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="previous_reading" id="prev_reading"
                                   class="form-control" placeholder="e.g. 150"
                                   min="0" step="0.001" required onchange="calcWater()">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Current Reading <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="current_reading" id="curr_reading"
                                   class="form-control" placeholder="e.g. 178"
                                   min="0" step="0.001" required onchange="calcWater()">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Rate per Unit (KES) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="rate_per_unit" id="rate"
                                   class="form-control" placeholder="e.g. 60"
                                   min="0" required onchange="calcWater()">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Reading Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="reading_date" class="form-control"
                                   value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="p-3 mb-3 d-flex justify-content-between align-items-center"
                         style="background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0">
                        <div>
                            <div style="font-size:.72rem;color:#6c757d">Units Consumed</div>
                            <div id="consumed_display" style="font-weight:700;color:#15803d">0</div>
                        </div>
                        <div class="text-end">
                            <div style="font-size:.72rem;color:#6c757d">Amount Charged</div>
                            <div id="charged_display" style="font-weight:700;color:#15803d">KES 0</div>
                        </div>
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
                        Record Reading
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function calcWater() {
    const prev = parseFloat(document.getElementById('prev_reading').value) || 0;
    const curr = parseFloat(document.getElementById('curr_reading').value) || 0;
    const rate = parseFloat(document.getElementById('rate').value)         || 0;

    const consumed = curr - prev;
    const charged  = consumed * rate;

    document.getElementById('consumed_display').textContent = consumed.toFixed(3);
    document.getElementById('charged_display').textContent  = 'KES ' + charged.toLocaleString();
}
</script>
@endpush

@endsection