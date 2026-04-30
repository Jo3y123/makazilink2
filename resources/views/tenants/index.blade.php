@extends('layouts.app')

@section('title', 'Tenants')
@section('page-title', 'Tenants')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">All Tenants</h2>
        <p class="text-muted mb-0" style="font-size:.82rem">Manage your tenants</p>
    </div>
    <a href="{{ route('tenants.create') }}" class="btn btn-sm"
       style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
        <i class="bi bi-plus-lg me-1"></i> Add Tenant
    </a>
</div>

@if($tenants->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-people"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">No tenants yet</h3>
        <p class="text-muted" style="font-size:.82rem">Click Add Tenant to register your first tenant</p>
        <div class="d-flex gap-2">
        <a href="{{ route('export.tenants') }}" class="btn btn-sm btn-outline-secondary"
           style="border-radius:8px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
        <a href="{{ route('tenants.create') }}" class="btn btn-sm"
           style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-plus-lg me-1"></i> Add Tenant
        </a>
    </div>
    </div>
@else
    <div class="card border-0 shadow-sm" style="border-radius:12px">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d">
                    <tr>
                        <th class="px-4 py-3">Tenant</th>
                        <th class="py-3">Phone</th>
                        <th class="py-3">Unit</th>
                        <th class="py-3">Lease Status</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody style="font-size:.85rem">
                    @foreach($tenants as $tenant)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:34px;height:34px;background:#e8f5ee;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#1a7a4a;flex-shrink:0">
                                    {{ strtoupper(substr($tenant->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;color:#1a1a2e">{{ $tenant->user->name }}</div>
                                    <div style="font-size:.75rem;color:#6c757d">{{ $tenant->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">{{ $tenant->user->phone ?? '—' }}</td>
                        <td class="py-3">
                            @if($tenant->activeLease)
                                <span style="font-weight:600">{{ $tenant->activeLease->unit->unit_number }}</span>
                                <span class="text-muted" style="font-size:.75rem"> — {{ $tenant->activeLease->unit->property->name }}</span>
                            @else
                                <span class="text-muted">No active lease</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($tenant->activeLease)
                                <span class="badge" style="background:#dcfce7;color:#15803d;border-radius:20px;font-size:.7rem">Active</span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#b91c1c;border-radius:20px;font-size:.7rem">No Lease</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <div class="d-flex gap-2">
                                <a href="{{ route('tenants.statement', $tenant) }}"
                                class="btn btn-sm btn-outline-secondary"
                                style="font-size:.75rem;border-radius:6px;padding:4px 12px"
                                title="Download Statement">
                                    <i class="bi bi-file-earmark-text"></i>
                                </a>
                                <a href="{{ route('tenants.edit', $tenant) }}"
                                class="btn btn-sm btn-outline-secondary"
                                style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('tenants.destroy', $tenant) }}" method="POST"
                                    onsubmit="return confirm('Remove {{ $tenant->user->name }}? This will delete their account.')">
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

@endsection