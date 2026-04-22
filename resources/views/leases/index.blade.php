@extends('layouts.app')

@section('title', 'Leases')
@section('page-title', 'Leases')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">All Leases</h2>
        <p class="text-muted mb-0" style="font-size:.82rem">Manage tenant lease agreements</p>
    </div>
    <a href="{{ route('leases.create') }}" class="btn btn-sm"
       style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
        <i class="bi bi-plus-lg me-1"></i> New Lease
    </a>
</div>

@if($leases->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-file-earmark-text"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">No leases yet</h3>
        <p class="text-muted" style="font-size:.82rem">Click New Lease to create your first lease</p>
        <a href="{{ route('leases.create') }}" class="btn btn-sm"
           style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-plus-lg me-1"></i> New Lease
        </a>
    </div>
@else
    <div class="card border-0 shadow-sm" style="border-radius:12px">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d">
                    <tr>
                        <th class="px-4 py-3">Tenant</th>
                        <th class="py-3">Unit</th>
                        <th class="py-3">Rent (KES)</th>
                        <th class="py-3">Start Date</th>
                        <th class="py-3">End Date</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody style="font-size:.85rem">
                    @foreach($leases as $lease)
                    <tr>
                        <td class="px-4 py-3">
                            <div style="font-weight:600;color:#1a1a2e">{{ $lease->tenant->user->name }}</div>
                            <div style="font-size:.75rem;color:#6c757d">{{ $lease->tenant->user->phone }}</div>
                        </td>
                        <td class="py-3">
                            <span style="font-weight:600">{{ $lease->unit->unit_number }}</span>
                            <span class="text-muted" style="font-size:.75rem"> — {{ $lease->unit->property->name }}</span>
                        </td>
                        <td class="py-3">{{ number_format($lease->monthly_rent) }}</td>
                        <td class="py-3">{{ $lease->start_date->format('d M Y') }}</td>
                        <td class="py-3">
                            {{ $lease->end_date ? $lease->end_date->format('d M Y') : 'Open ended' }}
                        </td>
                        <td class="py-3">
                            @if($lease->status === 'active')
                                <span class="badge" style="background:#dcfce7;color:#15803d;border-radius:20px;font-size:.7rem">Active</span>
                            @elseif($lease->status === 'expired')
                                <span class="badge" style="background:#fef3c7;color:#b45309;border-radius:20px;font-size:.7rem">Expired</span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#b91c1c;border-radius:20px;font-size:.7rem">Terminated</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <div class="d-flex gap-2">
                                <a href="{{ route('leases.show', $lease) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('leases.edit', $lease) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('leases.destroy', $lease) }}" method="POST"
                                      onsubmit="return confirm('Delete this lease?')">
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