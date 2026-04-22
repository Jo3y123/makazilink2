@extends('layouts.app')

@section('title', 'Maintenance Request')
@section('page-title', 'Maintenance Request')

@section('content')

<div class="d-flex justify-content-center">
    <div style="width:100%;max-width:680px">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('maintenance.index') }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">
                    {{ $maintenance->title }}
                </h2>
                <p class="text-muted mb-0" style="font-size:.8rem">
                    {{ $maintenance->unit->unit_number }} — {{ $maintenance->unit->property->name }}
                </p>
            </div>
            <div class="ms-auto">
                <a href="{{ route('maintenance.edit', $maintenance) }}"
                   class="btn btn-sm btn-outline-secondary"
                   style="border-radius:8px;font-size:.82rem">
                    <i class="bi bi-pencil me-1"></i>Update
                </a>
            </div>
        </div>

        {{-- Details Card --}}
        <div class="card border-0 shadow-sm mb-3" style="border-radius:12px">
            <div class="card-body p-4">

                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div style="font-size:.7rem;color:#6c757d;margin-bottom:2px">Status</div>
                        @if($maintenance->status === 'open')
                            <span class="badge" style="background:#fee2e2;color:#b91c1c;border-radius:20px;font-size:.75rem">Open</span>
                        @elseif($maintenance->status === 'in_progress')
                            <span class="badge" style="background:#fef3c7;color:#b45309;border-radius:20px;font-size:.75rem">In Progress</span>
                        @elseif($maintenance->status === 'resolved')
                            <span class="badge" style="background:#dcfce7;color:#15803d;border-radius:20px;font-size:.75rem">Resolved</span>
                        @else
                            <span class="badge" style="background:#f1f5f9;color:#64748b;border-radius:20px;font-size:.75rem">Closed</span>
                        @endif
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="font-size:.7rem;color:#6c757d;margin-bottom:2px">Priority</div>
                        @if($maintenance->priority === 'urgent')
                            <span class="badge" style="background:#fee2e2;color:#b91c1c;border-radius:20px;font-size:.75rem">Urgent</span>
                        @elseif($maintenance->priority === 'high')
                            <span class="badge" style="background:#fef3c7;color:#b45309;border-radius:20px;font-size:.75rem">High</span>
                        @elseif($maintenance->priority === 'medium')
                            <span class="badge" style="background:#dbeafe;color:#1e40af;border-radius:20px;font-size:.75rem">Medium</span>
                        @else
                            <span class="badge" style="background:#f1f5f9;color:#64748b;border-radius:20px;font-size:.75rem">Low</span>
                        @endif
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="font-size:.7rem;color:#6c757d;margin-bottom:2px">Category</div>
                        <div style="font-size:.82rem;font-weight:600;color:#1a1a2e">
                            {{ ucfirst(str_replace('_', ' ', $maintenance->category)) }}
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="font-size:.7rem;color:#6c757d;margin-bottom:2px">Date Logged</div>
                        <div style="font-size:.82rem;font-weight:600;color:#1a1a2e">
                            {{ $maintenance->created_at->format('d M Y') }}
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div style="font-size:.7rem;color:#6c757d;margin-bottom:4px">Description</div>
                    <p style="font-size:.85rem;color:#374151;margin:0">{{ $maintenance->description }}</p>
                </div>

                @if($maintenance->tenant)
                <div class="mb-3">
                    <div style="font-size:.7rem;color:#6c757d;margin-bottom:4px">Reported By</div>
                    <div style="font-size:.85rem;color:#374151">{{ $maintenance->tenant->user->name }}</div>
                </div>
                @endif

                @if($maintenance->cost)
                <div class="mb-3">
                    <div style="font-size:.7rem;color:#6c757d;margin-bottom:4px">Repair Cost</div>
                    <div style="font-size:.85rem;font-weight:700;color:#1a1a2e">KES {{ number_format($maintenance->cost) }}</div>
                </div>
                @endif

                @if($maintenance->resolution_notes)
                <div class="mb-3">
                    <div style="font-size:.7rem;color:#6c757d;margin-bottom:4px">Resolution Notes</div>
                    <p style="font-size:.85rem;color:#374151;margin:0">{{ $maintenance->resolution_notes }}</p>
                </div>
                @endif

            </div>
        </div>

        {{-- Photos --}}
        @if($maintenance->photos->count() > 0)
        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.8rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                    Photos ({{ $maintenance->photos->count() }})
                </p>
                <div class="row g-2">
                    @foreach($maintenance->photos as $photo)
                    <div class="col-6 col-md-4">
                        <div style="position:relative">
                            <img src="{{ asset('storage/' . $photo->file_path) }}"
                                 alt="{{ $photo->caption ?? 'Maintenance photo' }}"
                                 style="width:100%;height:160px;object-fit:cover;border-radius:8px;border:1px solid #e9ecef">
                            <span class="badge"
                                  style="position:absolute;top:8px;left:8px;background:rgba(0,0,0,.6);color:#fff;font-size:.65rem;border-radius:20px">
                                {{ ucfirst($photo->photo_type) }}
                            </span>
                        </div>
                        @if($photo->caption)
                            <div style="font-size:.72rem;color:#6c757d;margin-top:4px">{{ $photo->caption }}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

@endsection