@extends('layouts.app')

@section('title', 'Tenants')
@section('page-title', 'Tenants')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">All Tenants</h2>
        <p class="text-muted mb-0" style="font-size:.82rem">Manage your tenants</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('export.tenants') }}" class="btn btn-sm btn-outline-secondary"
           style="border-radius:8px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
        <a href="{{ route('tenants.quick-add') }}" class="btn btn-sm"
           style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-lightning me-1"></i> Quick Add Tenant
        </a>
        <a href="{{ route('tenants.create') }}" class="btn btn-sm btn-outline-secondary"
           style="border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-plus-lg me-1"></i> Full Form
        </a>
    </div>
</div>

@if($activeTenants->isEmpty() && $vacatedTenants->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-people"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">No tenants yet</h3>
        <p class="text-muted" style="font-size:.82rem">Add your first tenant to get started</p>
        <div class="d-flex gap-2 justify-content-center">
            <a href="{{ route('tenants.quick-add') }}" class="btn btn-sm"
               style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
                <i class="bi bi-lightning me-1"></i> Quick Add Tenant
            </a>
            <a href="{{ route('tenants.create') }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
                <i class="bi bi-plus-lg me-1"></i> Full Form
            </a>
        </div>
    </div>
@else

    {{-- Active Tenants --}}
    @if($activeTenants->isNotEmpty())
    <div class="mb-4">
        <p style="font-size:.8rem;font-weight:700;color:#1a1a2e;margin-bottom:12px">
            <i class="bi bi-person-check me-2 text-success"></i>
            Active Tenants ({{ $activeTenants->count() }})
        </p>
        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8fafc;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d">
                        <tr>
                            <th class="px-4 py-3">Tenant</th>
                            <th class="py-3">Phone</th>
                            <th class="py-3">Unit</th>
                            <th class="py-3">Rent</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:.85rem">
                        @foreach($activeTenants as $tenant)
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
                                @endif
                            </td>
                            <td class="py-3">
                                @if($tenant->activeLease)
                                    <span style="font-weight:600;color:#1a7a4a">
                                        KES {{ number_format($tenant->activeLease->monthly_rent) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($tenant->activeLease)
                                    @if($tenant->activeLease->end_date)
                                        <span class="badge" style="background:#dbeafe;color:#1e40af;border-radius:20px;font-size:.7rem">
                                            Fixed Term
                                        </span>
                                    @else
                                        <span class="badge" style="background:#dcfce7;color:#15803d;border-radius:20px;font-size:.7rem">
                                            Month to Month
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ route('tenants.statement', $tenant) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       style="font-size:.75rem;border-radius:6px;padding:4px 12px"
                                       title="Download Statement">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                    <a href="{{ route('tenants.edit', $tenant) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-warning"
                                            style="font-size:.75rem;border-radius:6px;padding:4px 12px"
                                            onclick="showVacateModal({{ $tenant->id }}, '{{ $tenant->user->name }}')">
                                        <i class="bi bi-box-arrow-right"></i> Vacate
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Vacated Tenants --}}
    @if($vacatedTenants->isNotEmpty())
    <div class="mb-4">
        <p style="font-size:.8rem;font-weight:700;color:#6c757d;margin-bottom:12px">
            <i class="bi bi-person-dash me-2"></i>
            Former Tenants ({{ $vacatedTenants->count() }})
        </p>
        <div class="card border-0 shadow-sm" style="border-radius:12px;opacity:.8">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8fafc;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d">
                        <tr>
                            <th class="px-4 py-3">Tenant</th>
                            <th class="py-3">Phone</th>
                            <th class="py-3">Last Unit</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:.85rem">
                        @foreach($vacatedTenants as $tenant)
                        <tr style="opacity:.75">
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:34px;height:34px;background:#f1f5f9;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#6c757d;flex-shrink:0">
                                        {{ strtoupper(substr($tenant->user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;color:#6c757d">{{ $tenant->user->name }}</div>
                                        <div style="font-size:.75rem;color:#9ca3af">{{ $tenant->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3" style="color:#9ca3af">{{ $tenant->user->phone ?? '—' }}</td>
                            <td class="py-3" style="color:#9ca3af">
                                @php
                                    $lastLease = $tenant->leases()->latest()->first();
                                @endphp
                                @if($lastLease)
                                    {{ $lastLease->unit->unit_number }} — {{ $lastLease->unit->property->name }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="badge" style="background:#f1f5f9;color:#6c757d;border-radius:20px;font-size:.7rem">
                                    Vacated
                                </span>
                            </td>
                            <td class="py-3">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('tenants.statement', $tenant) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       style="font-size:.75rem;border-radius:6px;padding:4px 12px"
                                       title="Download Statement">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

@endif

{{-- Vacate Modal --}}
<div class="modal fade" id="vacateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0">
                <h5 class="modal-title" style="font-size:.95rem;font-weight:700;color:#b45309">
                    <i class="bi bi-box-arrow-right me-2"></i>Vacate Tenant
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="vacateForm" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p style="font-size:.85rem;color:#6c757d;margin-bottom:16px">
                        You are vacating <strong id="vacateTenantName"></strong>.
                        Their account will be deactivated and the unit will be freed up.
                        All payment history will be preserved.
                    </p>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Vacate Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="vacate_date" class="form-control"
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Reason
                        </label>
                        <textarea name="vacate_reason" class="form-control" rows="2"
                                  placeholder="e.g. Lease ended, tenant requested to leave..."></textarea>
                    </div>
                    <div style="background:#fef3c7;border-radius:8px;padding:10px 14px;font-size:.78rem;color:#b45309">
                        <i class="bi bi-info-circle me-2"></i>
                        This will terminate their lease and mark the unit as vacant.
                        Their invoices and payment history will remain intact.
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-warning"
                            style="border-radius:8px;font-size:.85rem;font-weight:600;color:#fff">
                        <i class="bi bi-box-arrow-right me-1"></i>Confirm Vacate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showVacateModal(tenantId, tenantName) {
    document.getElementById('vacateTenantName').textContent = tenantName;
    document.getElementById('vacateForm').action = '/tenants/' + tenantId + '/vacate';
    new bootstrap.Modal(document.getElementById('vacateModal')).show();
}
</script>
@endpush

@endsection