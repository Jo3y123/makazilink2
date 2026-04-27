@extends('layouts.app')
 
@section('title', 'Subscription')
@section('page-title', 'Subscription')
 
@section('content')
 
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">Subscription Management</h2>
        <p class="text-muted mb-0" style="font-size:.82rem">Manage your system subscription</p>
    </div>
</div>
 
@if($subscription)
 
{{-- Status Cards --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Status</div>
                    <div class="stat-value" style="font-size:1.3rem;color:{{ $subscription->isActive() ? '#15803d' : '#b91c1c' }}">
                        {{ ucfirst($subscription->status) }}
                    </div>
                </div>
                <div class="stat-icon" style="background:{{ $subscription->isActive() ? '#dcfce7' : '#fee2e2' }};color:{{ $subscription->isActive() ? '#15803d' : '#b91c1c' }}">
                    <i class="bi bi-{{ $subscription->isActive() ? 'check-circle' : 'x-circle' }}"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Days Remaining</div>
                    <div class="stat-value" style="font-size:1.3rem;color:{{ $subscription->daysRemaining() < 7 ? '#b91c1c' : '#1a1a2e' }}">
                        {{ $subscription->daysRemaining() }}
                    </div>
                </div>
                <div class="stat-icon" style="background:#fef3c7;color:#b45309">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label">Units Used</div>
                    <div class="stat-value" style="font-size:1.3rem">
                        {{ $unitCount }} / {{ $subscription->max_units == 99999 ? '∞' : $subscription->max_units }}
                    </div>
                </div>
                <div class="stat-icon" style="background:#dbeafe;color:#1e40af">
                    <i class="bi bi-door-open"></i>
                </div>
            </div>
        </div>
    </div>
</div>
 
{{-- Subscription Info + Actions --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                    Current Subscription
                </p>
                <table class="table table-sm mb-0">
                    <tbody style="font-size:.85rem">
                        <tr>
                            <td style="border:none;color:#6c757d;padding:6px 0">Client</td>
                            <td style="border:none;font-weight:600;padding:6px 0">{{ $subscription->client_name }}</td>
                        </tr>
                        <tr>
                            <td style="border:none;color:#6c757d;padding:6px 0">Email</td>
                            <td style="border:none;font-weight:600;padding:6px 0">{{ $subscription->client_email }}</td>
                        </tr>
                        <tr>
                            <td style="border:none;color:#6c757d;padding:6px 0">Phone</td>
                            <td style="border:none;font-weight:600;padding:6px 0">{{ $subscription->client_phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td style="border:none;color:#6c757d;padding:6px 0">Plan</td>
                            <td style="border:none;font-weight:600;padding:6px 0">{{ $subscription->planLabel() }}</td>
                        </tr>
                        <tr>
                            <td style="border:none;color:#6c757d;padding:6px 0">Monthly Fee</td>
                            <td style="border:none;font-weight:600;padding:6px 0">KES {{ number_format($subscription->monthly_fee) }}</td>
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
            </div>
        </div>
    </div>
 
    <div class="col-12 col-md-6">
        {{-- Activate --}}
        <div class="card border-0 shadow-sm mb-3" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:12px">
                    <i class="bi bi-check-circle me-2 text-success"></i>Activate / Extend
                </p>
                <form action="{{ route('subscription.activate') }}" method="POST">
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
                            Activate
                        </button>
                    </div>
                </form>
            </div>
        </div>
 
        {{-- Suspend --}}
        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:12px">
                    <i class="bi bi-pause-circle me-2 text-danger"></i>Suspend Access
                </p>
                <p style="font-size:.8rem;color:#6c757d;margin-bottom:12px">
                    Suspending will immediately block all users from accessing the system.
                </p>
                <form action="{{ route('subscription.suspend') }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to suspend access?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger"
                            style="border-radius:8px;font-size:.82rem">
                        <i class="bi bi-pause-circle me-1"></i>Suspend
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
 
@endif
 
{{-- Setup / Edit Subscription --}}
<div class="card border-0 shadow-sm" style="border-radius:12px">
    <div class="card-body p-4">
        <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
            {{ $subscription ? 'Update Subscription' : 'Setup Subscription' }}
        </p>
        <form action="{{ route('subscription.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                        Client Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="client_name" class="form-control"
                           value="{{ $subscription?->client_name ?? '' }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                        Client Email <span class="text-danger">*</span>
                    </label>
                    <input type="email" name="client_email" class="form-control"
                           value="{{ $subscription?->client_email ?? '' }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                        Client Phone
                    </label>
                    <input type="text" name="client_phone" class="form-control"
                           value="{{ $subscription?->client_phone ?? '' }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                        Plan <span class="text-danger">*</span>
                    </label>
                    <select name="plan" class="form-select" required>
                        <option value="starter"    {{ ($subscription?->plan ?? '') === 'starter'    ? 'selected' : '' }}>Starter — up to 20 units (KES 2,500/mo)</option>
                        <option value="growth"     {{ ($subscription?->plan ?? '') === 'growth'     ? 'selected' : '' }}>Growth — up to 50 units (KES 5,000/mo)</option>
                        <option value="pro"        {{ ($subscription?->plan ?? '') === 'pro'        ? 'selected' : '' }}>Pro — up to 100 units (KES 8,000/mo)</option>
                        <option value="enterprise" {{ ($subscription?->plan ?? '') === 'enterprise' ? 'selected' : '' }}>Enterprise — unlimited (KES 15,000/mo)</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                        Status <span class="text-danger">*</span>
                    </label>
                    <select name="status" class="form-select" required>
                        <option value="trial"     {{ ($subscription?->status ?? '') === 'trial'     ? 'selected' : '' }}>Trial</option>
                        <option value="active"    {{ ($subscription?->status ?? '') === 'active'    ? 'selected' : '' }}>Active</option>
                        <option value="expired"   {{ ($subscription?->status ?? '') === 'expired'   ? 'selected' : '' }}>Expired</option>
                        <option value="suspended" {{ ($subscription?->status ?? '') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                        Monthly Fee (KES)
                    </label>
                    <input type="number" name="monthly_fee" class="form-control"
                           value="{{ $subscription?->monthly_fee ?? 2500 }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                        Trial Ends At
                    </label>
                    <input type="date" name="trial_ends_at" class="form-control"
                           value="{{ $subscription?->trial_ends_at?->format('Y-m-d') ?? now()->addDays(14)->format('Y-m-d') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                        Expires At
                    </label>
                    <input type="date" name="expires_at" class="form-control"
                           value="{{ $subscription?->expires_at?->format('Y-m-d') ?? '' }}">
                </div>
                <div class="col-12">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                        Notes
                    </label>
                    <textarea name="notes" class="form-control" rows="2"
                              placeholder="Optional notes about this client">{{ $subscription?->notes ?? '' }}</textarea>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn"
                        style="background:#1a7a4a;color:#fff;border-radius:8px;padding:10px 28px;font-size:.9rem;font-weight:600">
                    <i class="bi bi-check-lg me-2"></i>Save Subscription
                </button>
            </div>
        </form>
    </div>
</div>
 
@endsection
 