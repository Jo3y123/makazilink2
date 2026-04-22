@extends('layouts.app')

@section('title', 'Property Reports')
@section('page-title', 'Property Reports')

@section('content')

<div class="mb-4">
    <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">Property Reports</h2>
    <p class="text-muted mb-0" style="font-size:.82rem">View detailed reports for each property</p>
</div>

@if($properties->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-building"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">No properties found</h3>
        <p class="text-muted" style="font-size:.82rem">Add properties first to view reports</p>
    </div>
@else
    <div class="row g-3">
        @foreach($properties as $property)
        @php
            $total    = $property->units->count();
            $occupied = $property->units->where('status', 'occupied')->count();
            $vacant   = $property->units->where('status', 'vacant')->count();
            $rate     = $total > 0 ? round(($occupied / $total) * 100) : 0;
        @endphp
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div style="width:42px;height:42px;background:#e8f5ee;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#1a7a4a;font-size:1.1rem;flex-shrink:0">
                            <i class="bi bi-building"></i>
                        </div>
                        <span class="badge" style="background:#f0fdf4;color:#15803d;font-size:.7rem;font-weight:600;border-radius:20px;padding:4px 10px">
                            {{ $rate }}% Occupied
                        </span>
                    </div>

                    <h3 style="font-size:.95rem;font-weight:700;color:#1a1a2e;margin-bottom:4px">
                        {{ $property->name }}
                    </h3>
                    <p class="text-muted mb-3" style="font-size:.8rem">
                        <i class="bi bi-geo-alt me-1"></i>{{ $property->address }}
                    </p>

                    <div class="row g-2 mb-3">
                        <div class="col-4 text-center">
                            <div style="font-size:1.2rem;font-weight:700;color:#1a1a2e">{{ $total }}</div>
                            <div style="font-size:.7rem;color:#6c757d">Total</div>
                        </div>
                        <div class="col-4 text-center">
                            <div style="font-size:1.2rem;font-weight:700;color:#1a7a4a">{{ $occupied }}</div>
                            <div style="font-size:.7rem;color:#6c757d">Occupied</div>
                        </div>
                        <div class="col-4 text-center">
                            <div style="font-size:1.2rem;font-weight:700;color:#b45309">{{ $vacant }}</div>
                            <div style="font-size:.7rem;color:#6c757d">Vacant</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('reports.property.show', $property) }}"
                           class="btn btn-sm flex-fill"
                           style="background:#1a7a4a;color:#fff;border-radius:8px;font-size:.78rem;font-weight:600">
                            <i class="bi bi-eye me-1"></i>View Report
                        </a>
                        <a href="{{ route('reports.property.pdf', $property) }}"
                           class="btn btn-sm btn-outline-secondary"
                           style="border-radius:8px;font-size:.78rem">
                            <i class="bi bi-file-pdf"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection