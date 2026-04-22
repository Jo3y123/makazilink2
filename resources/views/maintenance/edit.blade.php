@extends('layouts.app')

@section('title', 'Update Maintenance Request')
@section('page-title', 'Update Maintenance Request')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('maintenance.show', $maintenance) }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">Update Request</h2>
                <p class="text-muted mb-0" style="font-size:.8rem">{{ $maintenance->title }}</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <form action="{{ route('maintenance.update', $maintenance) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" class="form-select" required>
                                <option value="open"        {{ $maintenance->status === 'open'        ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $maintenance->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved"    {{ $maintenance->status === 'resolved'    ? 'selected' : '' }}>Resolved</option>
                                <option value="closed"      {{ $maintenance->status === 'closed'      ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Priority <span class="text-danger">*</span>
                            </label>
                            <select name="priority" class="form-select" required>
                                <option value="low"    {{ $maintenance->priority === 'low'    ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ $maintenance->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high"   {{ $maintenance->priority === 'high'   ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ $maintenance->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Repair Cost (KES)
                        </label>
                        <input type="number" name="cost" class="form-control"
                               value="{{ old('cost', $maintenance->cost) }}"
                               placeholder="e.g. 5000" min="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Resolution Notes
                        </label>
                        <textarea name="resolution_notes" class="form-control" rows="3"
                                  placeholder="Describe what was done to fix the issue">{{ old('resolution_notes', $maintenance->resolution_notes) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Photo Type
                        </label>
                        <select name="photo_type" class="form-select">
                            <option value="during">During Repair</option>
                            <option value="after">After Repair</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Add More Photos
                        </label>
                        <input type="file" name="photos[]" class="form-control"
                               multiple accept="image/*">
                        <small class="text-muted" style="font-size:.72rem">
                            Max 2MB each.
                        </small>
                    </div>

                    <button type="submit" class="btn w-100"
                            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:11px;font-size:.9rem;font-weight:600;">
                        <i class="bi bi-check-lg me-2"></i>Save Update
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection