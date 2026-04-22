@extends('layouts.app')

@section('title', 'Lease Details')
@section('page-title', 'Lease Details')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-8">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('leases.index') }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">Lease Details</h2>
                <p class="text-muted mb-0" style="font-size:.8rem">
                    {{ $lease->tenant->user->name }} — {{ $lease->unit->unit_number }}
                </p>
            </div>
            <div class="ms-auto d-flex gap-2">
                <a href="{{ route('leases.edit', $lease) }}"
                   class="btn btn-sm btn-outline-secondary"
                   style="border-radius:8px;font-size:.82rem">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
            </div>
        </div>

        <div class="row g-3">

            {{-- Tenant Info --}}
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius:12px">
                    <div class="card-body p-4">
                        <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6c757d;margin-bottom:16px">
                            Tenant
                        </p>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width:42px;height:42px;background:#e8f5ee;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;color:#1a7a4a">
                                {{ strtoupper(substr($lease->tenant->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight:700;color:#1a1a2e">{{ $lease->tenant->user->name }}</div>
                                <div style="font-size:.78rem;color:#6c757d">{{ $lease->tenant->user->email }}</div>
                            </div>
                        </div>
                        <div style="font-size:.82rem;color:#374151">
                            <div class="mb-1"><i class="bi bi-phone me-2 text-muted"></i>{{ $lease->tenant->user->phone ?? '—' }}</div>
                            <div class="mb-1"><i class="bi bi-card-text me-2 text-muted"></i>ID: {{ $lease->tenant->id_number ?? '—' }}</div>
                            <div><i class="bi bi-briefcase me-2 text-muted"></i>{{ $lease->tenant->occupation ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Unit Info --}}
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius:12px">
                    <div class="card-body p-4">
                        <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6c757d;margin-bottom:16px">
                            Unit
                        </p>
                        <div style="font-size:.82rem;color:#374151">
                            <div class="mb-2">
                                <span style="font-size:1.2rem;font-weight:700;color:#1a1a2e">
                                    {{ $lease->unit->unit_number }}
                                </span>
                            </div>
                            <div class="mb-1"><i class="bi bi-building me-2 text-muted"></i>{{ $lease->unit->property->name }}</div>
                            <div class="mb-1"><i class="bi bi-geo-alt me-2 text-muted"></i>{{ $lease->unit->property->address }}</div>
                            <div><i class="bi bi-door-open me-2 text-muted"></i>{{ ucfirst(str_replace('_', ' ', $lease->unit->type)) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lease Info --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius:12px">
                    <div class="card-body p-4">
                        <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6c757d;margin-bottom:16px">
                            Lease Terms
                        </p>
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <div style="font-size:.72rem;color:#6c757d;margin-bottom:2px">Monthly Rent</div>
                                <div style="font-weight:700;color:#1a1a2e">KES {{ number_format($lease->monthly_rent) }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div style="font-size:.72rem;color:#6c757d;margin-bottom:2px">Deposit Paid</div>
                                <div style="font-weight:700;color:#1a1a2e">KES {{ number_format($lease->deposit_paid) }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div style="font-size:.72rem;color:#6c757d;margin-bottom:2px">Start Date</div>
                                <div style="font-weight:700;color:#1a1a2e">{{ $lease->start_date->format('d M Y') }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div style="font-size:.72rem;color:#6c757d;margin-bottom:2px">End Date</div>
                                <div style="font-weight:700;color:#1a1a2e">
                                    {{ $lease->end_date ? $lease->end_date->format('d M Y') : 'Open ended' }}
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div style="font-size:.72rem;color:#6c757d;margin-bottom:2px">Notice Period</div>
                                <div style="font-weight:700;color:#1a1a2e">{{ $lease->notice_days }} days</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div style="font-size:.72rem;color:#6c757d;margin-bottom:2px">Status</div>
                                <div>
                                    @if($lease->status === 'active')
                                        <span class="badge" style="background:#dcfce7;color:#15803d;border-radius:20px;font-size:.7rem">Active</span>
                                    @elseif($lease->status === 'expired')
                                        <span class="badge" style="background:#fef3c7;color:#b45309;border-radius:20px;font-size:.7rem">Expired</span>
                                    @else
                                        <span class="badge" style="background:#fee2e2;color:#b91c1c;border-radius:20px;font-size:.7rem">Terminated</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($lease->terms)
                        <hr style="border-color:#f0f0f0;margin:16px 0">
                        <div style="font-size:.72rem;color:#6c757d;margin-bottom:6px">Terms</div>
                        <p style="font-size:.82rem;color:#374151;margin:0">{{ $lease->terms }}</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection