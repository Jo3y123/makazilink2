@extends('layouts.app')

@section('title', 'Maintenance')
@section('page-title', 'Maintenance Requests')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">Maintenance Requests</h2>
        <p class="text-muted mb-0" style="font-size:.82rem">Track and manage repair requests</p>
    </div>
    <a href="{{ route('maintenance.create') }}" class="btn btn-sm"
       style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
        <i class="bi bi-plus-lg me-1"></i> New Request
    </a>
</div>

@if($requests->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-tools"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">No maintenance requests</h3>
        <p class="text-muted" style="font-size:.82rem">Click New Request to log a maintenance issue</p>
        <a href="{{ route('maintenance.create') }}" class="btn btn-sm"
           style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-plus-lg me-1"></i> New Request
        </a>
    </div>
@else
    <div class="card border-0 shadow-sm" style="border-radius:12px">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d">
                    <tr>
                        <th class="px-4 py-3">Title</th>
                        <th class="py-3">Unit</th>
                        <th class="py-3">Category</th>
                        <th class="py-3">Priority</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Date</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody style="font-size:.85rem">
                    @foreach($requests as $request)
                    <tr>
                        <td class="px-4 py-3">
                            <div style="font-weight:600;color:#1a1a2e">{{ $request->title }}</div>
                            @if($request->tenant)
                                <div style="font-size:.75rem;color:#6c757d">{{ $request->tenant->user->name }}</div>
                            @endif
                        </td>
                        <td class="py-3">
                            {{ $request->unit->unit_number }}
                            <span class="text-muted" style="font-size:.75rem"> — {{ $request->unit->property->name }}</span>
                        </td>
                        <td class="py-3">{{ ucfirst(str_replace('_', ' ', $request->category)) }}</td>
                        <td class="py-3">
                            @if($request->priority === 'urgent')
                                <span class="badge" style="background:#fee2e2;color:#b91c1c;border-radius:20px;font-size:.7rem">Urgent</span>
                            @elseif($request->priority === 'high')
                                <span class="badge" style="background:#fef3c7;color:#b45309;border-radius:20px;font-size:.7rem">High</span>
                            @elseif($request->priority === 'medium')
                                <span class="badge" style="background:#dbeafe;color:#1e40af;border-radius:20px;font-size:.7rem">Medium</span>
                            @else
                                <span class="badge" style="background:#f1f5f9;color:#64748b;border-radius:20px;font-size:.7rem">Low</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($request->status === 'open')
                                <span class="badge" style="background:#fee2e2;color:#b91c1c;border-radius:20px;font-size:.7rem">Open</span>
                            @elseif($request->status === 'in_progress')
                                <span class="badge" style="background:#fef3c7;color:#b45309;border-radius:20px;font-size:.7rem">In Progress</span>
                            @elseif($request->status === 'resolved')
                                <span class="badge" style="background:#dcfce7;color:#15803d;border-radius:20px;font-size:.7rem">Resolved</span>
                            @else
                                <span class="badge" style="background:#f1f5f9;color:#64748b;border-radius:20px;font-size:.7rem">Closed</span>
                            @endif
                        </td>
                        <td class="py-3">{{ $request->created_at->format('d M Y') }}</td>
                        <td class="py-3">
                            <div class="d-flex gap-2">
                                <a href="{{ route('maintenance.show', $request) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('maintenance.edit', $request) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('maintenance.destroy', $request) }}" method="POST"
                                      onsubmit="return confirm('Delete this request?')">
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