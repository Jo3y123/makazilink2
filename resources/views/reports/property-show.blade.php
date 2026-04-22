@extends('layouts.app')

@section('title', 'Property Report - ' . $property->name)
@section('page-title', 'Property Report')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('reports.properties') }}" class="btn btn-sm btn-outline-secondary"
       style="border-radius:8px">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">{{ $property->name }}</h2>
        <p class="text-muted mb-0" style="font-size:.8rem">{{ $property->address }}</p>
    </div>
    <div class="ms-auto">
        <a href="{{ route('reports.property.pdf', $property) }}"
           class="btn btn-sm btn-outline-secondary"
           style="border-radius:8px;font-size:.82rem">
            <i class="bi bi-file-pdf me-1"></i>Download PDF
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Total Units</div>
                    <div class="stat-value">{{ $totalUnits }}</div>
                </div>
                <div class="stat-icon" style="background:#e8f5ee;color:#1a7a4a">
                    <i class="bi bi-door-open"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Occupancy</div>
                    <div class="stat-value">{{ $occupancyRate }}%</div>
                </div>
                <div class="stat-icon" style="background:#dbeafe;color:#1e40af">
                    <i class="bi bi-building-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">This Month</div>
                    <div class="stat-value" style="font-size:1.2rem">
                        KES {{ number_format($thisMonthRevenue) }}
                    </div>
                </div>
                <div class="stat-icon" style="background:#dcfce7;color:#15803d">
                    <i class="bi bi-cash-coin"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Outstanding</div>
                    <div class="stat-value" style="font-size:1.2rem;color:#b91c1c">
                        KES {{ number_format($outstandingBalance) }}
                    </div>
                </div>
                <div class="stat-icon" style="background:#fee2e2;color:#b91c1c">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Units Table --}}
<div class="card border-0 shadow-sm" style="border-radius:12px">
    <div class="card-body p-4">
        <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
            Units ({{ $totalUnits }})
        </p>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d">
                    <tr>
                        <th class="px-4 py-3">Unit</th>
                        <th class="py-3">Type</th>
                        <th class="py-3">Rent (KES)</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Tenant</th>
                        <th class="py-3">Lease Start</th>
                    </tr>
                </thead>
                <tbody style="font-size:.85rem">
                    @foreach($units as $unit)
                    <tr>
                        <td class="px-4 py-3">
                            <span style="font-weight:700;color:#1a1a2e">{{ $unit->unit_number }}</span>
                        </td>
                        <td class="py-3">{{ ucfirst(str_replace('_', ' ', $unit->type)) }}</td>
                        <td class="py-3">{{ number_format($unit->rent_amount) }}</td>
                        <td class="py-3">
                            @if($unit->status === 'occupied')
                                <span class="badge" style="background:#dcfce7;color:#15803d;border-radius:20px;font-size:.7rem">Occupied</span>
                            @elseif($unit->status === 'vacant')
                                <span class="badge" style="background:#fef3c7;color:#b45309;border-radius:20px;font-size:.7rem">Vacant</span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#b91c1c;border-radius:20px;font-size:.7rem">Maintenance</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($unit->activeLease)
                                <div style="font-weight:600">{{ $unit->activeLease->tenant->user->name }}</div>
                                <div style="font-size:.75rem;color:#6c757d">{{ $unit->activeLease->tenant->user->phone }}</div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($unit->activeLease)
                                {{ $unit->activeLease->start_date->format('d M Y') }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection