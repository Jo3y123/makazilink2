@extends('layouts.app')

@section('title', 'Superadmin Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="mb-4">
    <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">
        Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
        {{ $user->name }} 👋
    </h2>
    <p class="text-muted mb-0" style="font-size:.82rem">
        {{ now()->format('l, d F Y') }} — MakaziLink Superadmin Panel
    </p>
</div>

@if(session('success'))
<div class="alert alert-success mb-4" style="border-radius:10px;font-size:.85rem">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger mb-4" style="border-radius:10px;font-size:.85rem">
    @foreach($errors->all() as $error)
        <div><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</div>
    @endforeach
</div>
@endif

@if($subscription)

{{-- Warning if expiring in 3 days or less --}}
@if(!$subscription->is_exempt && $subscription->daysRemaining() <= 3 && $subscription->daysRemaining() >= 0)
<div class="alert mb-4" style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:16px 20px">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill" style="color:#b91c1c;font-size:1rem"></i>
            <span style="font-weight:700;color:#b91c1c;font-size:.9rem">
                @if($subscription->daysRemaining() === 0)
                    ⚠️ Client subscription expires TODAY!
                @elseif($subscription->daysRemaining() === 1)
                    ⚠️ Client subscription expires TOMORROW!
                @else
                    ⚠️ Client subscription expires in {{ $subscription->daysRemaining() }} days!
                @endif
            </span>
        </div>
        {{-- Quick activate --}}
        <form action="{{ route('subscription.activate') }}" method="POST" class="d-flex gap-2">
            @csrf
            <input type="hidden" name="days" value="30">
            <button type="submit" class="btn btn-sm"
                    style="background:#1a7a4a;color:#fff;border-radius:8px;padding:6px 16px;font-size:.82rem;white-space:nowrap">
                <i class="bi bi-lightning-fill me-1"></i>Quick Activate 30 Days
            </button>
        </form>
    </div>
</div>
@endif

{{-- Status Cards --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Status</div>
                    <div class="stat-value" style="font-size:1.2rem;color:{{ $subscription->isActive() ? '#15803d' : '#b91c1c' }}">
                        {{ ucfirst($subscription->status) }}
                        @if($subscription->is_exempt)
                            <span style="font-size:.7rem;background:#e8f5ee;color:#1a7a4a;border-radius:20px;padding:2px 8px;font-weight:600;margin-left:4px">Exempt</span>
                        @endif
                    </div>
                </div>
                <div class="stat-icon" style="background:{{ $subscription->isActive() ? '#dcfce7' : '#fee2e2' }};color:{{ $subscription->isActive() ? '#15803d' : '#b91c1c' }}">
                    <i class="bi bi-{{ $subscription->isActive() ? 'check-circle' : 'x-circle' }}"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Days Remaining</div>
                    <div class="stat-value" style="font-size:1.3rem;color:{{ $subscription->daysRemaining() < 7 ? '#b91c1c' : '#1a1a2e' }}">
                        {{ $subscription->daysRemaining() }}
                    </div>
                    <div style="font-size:.72rem;color:#6c757d;margin-top:2px">
                        Expires {{ $subscription->expires_at ? $subscription->expires_at->format('d M Y') : '—' }}
                    </div>
                </div>
                <div class="stat-icon" style="background:#fef3c7;color:#b45309">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Monthly Fee</div>
                    <div class="stat-value" style="font-size:1.3rem;color:#1a7a4a">
                        {{ $currency }} {{ number_format($calculatedFee) }}
                    </div>
                    <div style="font-size:.72rem;color:#6c757d;margin-top:2px">
                        {{ $tenantCount }} tenants × KES 100
                    </div>
                </div>
                <div class="stat-icon" style="background:#dcfce7;color:#15803d">
                    <i class="bi bi-cash-coin"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Active Tenants</div>
                    <div class="stat-value" style="font-size:1.3rem">
                        {{ $tenantCount }}
                    </div>
                    <div style="font-size:.72rem;color:#6c757d;margin-top:2px">
                        Plan: {{ $subscription->planLabel() }}
                    </div>
                </div>
                <div class="stat-icon" style="background:#dbeafe;color:#1e40af">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">

    {{-- Left Column --}}
    <div class="col-12 col-md-6">

        {{-- Client Details --}}
        <div class="card border-0 shadow-sm mb-3" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                    <i class="bi bi-person-circle me-2 text-primary"></i>Client Details
                </p>
                <table class="table table-sm mb-0">
                    <tbody style="font-size:.85rem">
                        <tr>
                            <td style="border:none;color:#6c757d;padding:6px 0;width:40%">Name</td>
                            <td style="border:none;font-weight:600;padding:6px 0">{{ $subscription->client_name }}</td>
                        </tr>
                        <tr>
                            <td style="border:none;color:#6c757d;padding:6px 0">Email</td>
                            <td style="border:none;font-weight:600;padding:6px 0">
                                <a href="mailto:{{ $subscription->client_email }}" style="color:#1a7a4a">
                                    {{ $subscription->client_email }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:none;color:#6c757d;padding:6px 0">Phone</td>
                            <td style="border:none;font-weight:600;padding:6px 0">
                                @if($subscription->client_phone)
                                <a href="tel:{{ $subscription->client_phone }}" style="color:#1a7a4a">
                                    {{ $subscription->client_phone }}
                                </a>
                                @else
                                —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="border:none;color:#6c757d;padding:6px 0">Trial Ends</td>
                            <td style="border:none;font-weight:600;padding:6px 0">
                                {{ $subscription->trial_ends_at ? $subscription->trial_ends_at->format('d M Y') : '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border:none;color:#6c757d;padding:6px 0">Expires</td>
                            <td style="border:none;font-weight:600;padding:6px 0">
                                {{ $subscription->expires_at ? $subscription->expires_at->format('d M Y') : '—' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-3 d-flex gap-2 flex-wrap">
                    <a href="mailto:{{ $subscription->client_email }}"
                       style="background:#dbeafe;color:#1e40af;border-radius:8px;padding:6px 14px;font-size:.78rem;font-weight:600;text-decoration:none">
                        <i class="bi bi-envelope me-1"></i>Email
                    </a>
                    @if($subscription->client_phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $subscription->client_phone) }}"
                       target="_blank"
                       style="background:#dcfce7;color:#15803d;border-radius:8px;padding:6px 14px;font-size:.78rem;font-weight:600;text-decoration:none">
                        <i class="bi bi-whatsapp me-1"></i>WhatsApp
                    </a>
                    @endif
                    <button data-bs-toggle="modal" data-bs-target="#editClientModal"
                            style="background:#f3e8ff;color:#7e22ce;border:none;border-radius:8px;padding:6px 14px;font-size:.78rem;font-weight:600;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif">
                        <i class="bi bi-pencil me-1"></i>Edit Details
                    </button>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:12px">
                    <i class="bi bi-sticky me-2 text-warning"></i>Private Notes
                </p>
                <form action="{{ route('subscription.notes') }}" method="POST">
                    @csrf
                    <textarea name="notes" class="form-control mb-3" rows="4"
                              placeholder="Notes about this client — payment history, special arrangements, issues..."
                              style="font-size:.82rem;border-radius:8px">{{ $adminNotes }}</textarea>
                    <button type="submit" class="btn btn-sm btn-outline-secondary"
                            style="border-radius:8px;font-size:.82rem">
                        <i class="bi bi-save me-1"></i>Save Notes
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- Right Column --}}
    <div class="col-12 col-md-6">

        {{-- Quick Actions --}}
        <div class="card border-0 shadow-sm mb-3" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:12px">
                    <i class="bi bi-lightning me-2 text-warning"></i>Quick Actions
                </p>

                {{-- Activate --}}
                <form action="{{ route('subscription.activate') }}" method="POST" class="mb-3">
                    @csrf
                    <div class="d-flex gap-2">
                        <select name="days" class="form-select form-select-sm" style="border-radius:8px">
                            <option value="30">30 days — 1 month</option>
                            <option value="60">60 days — 2 months</option>
                            <option value="90">90 days — 3 months</option>
                            <option value="180">180 days — 6 months</option>
                            <option value="365">365 days — 1 year</option>
                        </select>
                        <button type="submit" class="btn btn-sm"
                                style="background:#1a7a4a;color:#fff;border-radius:8px;padding:6px 16px;font-size:.82rem;white-space:nowrap">
                            <i class="bi bi-check-circle me-1"></i>Activate
                        </button>
                    </div>
                </form>

                {{-- Suspend --}}
                <form action="{{ route('subscription.suspend') }}" method="POST" class="mb-3"
                      onsubmit="return confirm('Suspend this client?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100"
                            style="border-radius:8px;font-size:.82rem">
                        <i class="bi bi-pause-circle me-1"></i>Suspend Access
                    </button>
                </form>

                {{-- Exempt toggle --}}
                <form action="{{ route('subscription.activate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="exempt_only" value="1">
                    <div style="background:#f0fdf4;border-radius:8px;padding:12px;border:1px solid #86efac">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="is_exempt" id="is_exempt" value="1"
                                   {{ $subscription->is_exempt ? 'checked' : '' }}
                                   onchange="this.form.submit()">
                            <label class="form-check-label" for="is_exempt"
                                   style="font-size:.82rem;font-weight:600;color:#15803d">
                                Exempt from subscription checks
                            </label>
                        </div>
                        <div style="font-size:.72rem;color:#6c757d;margin-top:4px;padding-left:24px">
                            When ticked client never gets locked out
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:12px">
                    <i class="bi bi-key me-2 text-danger"></i>Change My Password
                </p>
                <form action="{{ route('subscription.password') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <input type="password" name="current_password" class="form-control form-control-sm"
                               placeholder="Current password" style="border-radius:8px" required>
                        @error('current_password')
                            <div style="color:#b91c1c;font-size:.75rem;margin-top:4px">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <input type="password" name="new_password" class="form-control form-control-sm"
                               placeholder="New password (min 8 characters)" style="border-radius:8px" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="new_password_confirmation" class="form-control form-control-sm"
                               placeholder="Confirm new password" style="border-radius:8px" required>
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100"
                            style="border-radius:8px;font-size:.82rem">
                        <i class="bi bi-key me-1"></i>Change Password
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

{{-- My Settings --}}
<div class="card border-0 shadow-sm mb-3" style="border-radius:12px">
    <div class="card-body p-4">
        <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
            <i class="bi bi-gear me-2 text-secondary"></i>My Settings
        </p>
        <form action="{{ route('subscription.settings') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                        Payment Type
                    </label>
                    <select name="my_paybill_type" class="form-select form-select-sm" style="border-radius:8px">
                        <option value="paybill"  {{ $myPaybillType === 'paybill' ? 'selected' : '' }}>Paybill</option>
                        <option value="till"     {{ $myPaybillType === 'till'    ? 'selected' : '' }}>Till Number</option>
                        <option value="phone"    {{ $myPaybillType === 'phone'   ? 'selected' : '' }}>Phone Number</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                        Your M-Pesa Number
                    </label>
                    <input type="text" name="my_paybill" class="form-control form-control-sm"
                           value="{{ $myPaybill }}"
                           placeholder="e.g. 247247 or 0712345678"
                           style="border-radius:8px">
                    <small style="font-size:.72rem;color:#6c757d">
                        Clients send subscription payments here
                    </small>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                        System Version
                    </label>
                    <input type="text" name="system_version" class="form-control form-control-sm"
                           value="{{ $systemVersion }}"
                           placeholder="e.g. 1.0.0"
                           style="border-radius:8px">
                    <small style="font-size:.72rem;color:#6c757d">
                        Shown in system settings
                    </small>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-sm btn-outline-secondary"
                        style="border-radius:8px;font-size:.82rem">
                    <i class="bi bi-save me-1"></i>Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Renewal History --}}
<div class="card border-0 shadow-sm" style="border-radius:12px">
    <div class="card-body p-4">
        <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
            <i class="bi bi-clock-history me-2 text-primary"></i>Renewal History
        </p>
        @if($renewalHistory->isEmpty())
            <p class="text-muted" style="font-size:.82rem">No renewal history yet. History will appear here after first activation.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead style="font-size:.72rem;text-transform:uppercase;color:#6c757d">
                        <tr>
                            <th style="border:none">Date Activated</th>
                            <th style="border:none">Days Added</th>
                            <th style="border:none">From</th>
                            <th style="border:none">To</th>
                            <th style="border:none">By</th>
                            <th style="border:none">Method</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:.82rem">
                        @foreach($renewalHistory as $history)
                        <tr>
                            <td style="border-color:#f0f0f0">{{ $history->created_at->format('d M Y') }}</td>
                            <td style="border-color:#f0f0f0;font-weight:600;color:#1a7a4a">{{ $history->days_added }} days</td>
                            <td style="border-color:#f0f0f0">{{ $history->activated_from->format('d M Y') }}</td>
                            <td style="border-color:#f0f0f0">{{ $history->activated_to->format('d M Y') }}</td>
                            <td style="border-color:#f0f0f0">{{ $history->activated_by ?? '—' }}</td>
                            <td style="border-color:#f0f0f0">
                                <span class="badge" style="background:#dbeafe;color:#1e40af;border-radius:20px;font-size:.7rem">
                                    {{ ucfirst($history->method) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@else

{{-- No subscription set up yet --}}
<div class="card border-0 shadow-sm" style="border-radius:12px">
    <div class="card-body p-5 text-center">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-credit-card"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">No Subscription Set Up</h3>
        <p class="text-muted" style="font-size:.82rem">
            Go to Settings → Subscription to set up this client's subscription.
        </p>
        <a href="{{ route('subscription.index') }}" class="btn btn-sm"
           style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600">
            <i class="bi bi-plus-lg me-1"></i> Set Up Subscription
        </a>
    </div>
</div>

@endif

{{-- Edit Client Details Modal --}}
<div class="modal fade" id="editClientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0">
                <h5 class="modal-title" style="font-size:.95rem;font-weight:700">
                    <i class="bi bi-pencil me-2"></i>Edit Client Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('subscription.store') }}" method="POST">
                @csrf
                <input type="hidden" name="plan"          value="{{ $subscription->plan }}">
                <input type="hidden" name="status"        value="{{ $subscription->status }}">
                <input type="hidden" name="monthly_fee"   value="{{ $subscription->monthly_fee }}">
                <input type="hidden" name="trial_ends_at" value="{{ $subscription->trial_ends_at?->format('Y-m-d') }}">
                <input type="hidden" name="expires_at"    value="{{ $subscription->expires_at?->format('Y-m-d') }}">
                <input type="hidden" name="notes"         value="{{ $subscription->notes }}">
                <input type="hidden" name="is_exempt"     value="{{ $subscription->is_exempt ? '1' : '0' }}">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Client Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="client_name" class="form-control"
                               value="{{ $subscription->client_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Client Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" name="client_email" class="form-control"
                               value="{{ $subscription->client_email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Client Phone
                        </label>
                        <input type="text" name="client_phone" class="form-control"
                               value="{{ $subscription->client_phone }}">
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm"
                            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:6px 20px;font-size:.85rem;font-weight:600;">
                        <i class="bi bi-check-lg me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection