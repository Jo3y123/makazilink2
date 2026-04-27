@extends('layouts.app')

@section('title', 'Properties')
@section('page-title', 'Properties')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">All Properties</h2>
        <p class="text-muted mb-0" style="font-size:.82rem">Manage your rental properties</p>
    </div>
    <a href="{{ route('properties.create') }}" class="btn btn-sm"
       style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
        <i class="bi bi-plus-lg me-1"></i> Add Property
    </a>
</div>

@if($properties->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-building"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">No properties yet</h3>
        <p class="text-muted" style="font-size:.82rem">Click Add Property to get started</p>
        <a href="{{ route('properties.create') }}" class="btn btn-sm"
           style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-plus-lg me-1"></i> Add Property
        </a>
    </div>
@else
    <div class="row g-3">
        @foreach($properties as $property)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;overflow:hidden">

                {{-- Property Image --}}
                @if($property->image_path)
                    <img src="{{ asset('storage/' . $property->image_path) }}"
                         alt="{{ $property->name }}"
                         style="width:100%;height:160px;object-fit:cover">
                @else
                    <div style="width:100%;height:160px;background:linear-gradient(135deg,#0f2d1e,#1a7a4a);display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-building" style="font-size:2.5rem;color:rgba(255,255,255,.3)"></i>
                    </div>
                @endif

                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <h3 style="font-size:.95rem;font-weight:700;color:#1a1a2e;margin-bottom:0">
                            {{ $property->name }}
                        </h3>
                        <span class="badge" style="background:#f0fdf4;color:#15803d;font-size:.7rem;font-weight:600;border-radius:20px;padding:4px 10px;flex-shrink:0;margin-left:8px">
                            {{ ucfirst(str_replace('_', ' ', $property->type)) }}
                        </span>
                    </div>

                    <p class="text-muted mb-1" style="font-size:.8rem">
                        <i class="bi bi-geo-alt me-1"></i>{{ $property->address }}
                    </p>
                    @if($property->town || $property->county)
                    <p class="text-muted mb-3" style="font-size:.78rem">
                        {{ $property->town }}{{ $property->town && $property->county ? ', ' : '' }}{{ $property->county }}
                    </p>
                    @endif

                    @php
                        $occupied = $property->units->where('status', 'occupied')->count();
                        $total    = $property->units->count();
                        $vacant   = $property->units->where('status', 'vacant')->count();
                    @endphp

                    <div class="d-flex gap-3 mb-3" style="font-size:.78rem">
                        <span style="color:#1a7a4a">
                            <i class="bi bi-door-closed me-1"></i>{{ $occupied }} occupied
                        </span>
                        <span style="color:#b45309">
                            <i class="bi bi-door-open me-1"></i>{{ $vacant }} vacant
                        </span>
                        <span style="color:#6c757d">
                            {{ $total }} total
                        </span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-3"
                         style="border-top:1px solid #f0f0f0">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('properties.water', $property) }}"
                            class="btn btn-sm"
                            style="background:#0ea5e9;color:#fff;font-size:.75rem;border-radius:6px;padding:4px 12px"
                            title="Shared Water Billing">
                                <i class="bi bi-droplet me-1"></i> Water
                            </a>
                            <a href="{{ route('properties.edit', $property) }}"
                            class="btn btn-sm btn-outline-secondary"
                            style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        @if(auth()->user()->isAdmin())
                        <form action="{{ route('properties.destroy', $property) }}" method="POST"
                              onsubmit="return confirm('Delete {{ $property->name }}? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection