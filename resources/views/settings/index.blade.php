@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')

<div class="mb-4">
    <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">System Settings</h2>
    <p class="text-muted mb-0" style="font-size:.82rem">Configure MakaziLink v2 for your business</p>
</div>

<form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">

        {{-- General Settings --}}
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="width:36px;height:36px;background:#e8f5ee;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#1a7a4a">
                            <i class="bi bi-gear"></i>
                        </div>
                        <h3 style="font-size:.95rem;font-weight:700;color:#1a1a2e;margin:0">General</h3>
                    </div>

                    {{-- Logo Upload --}}
                    <div class="mb-4 p-3" style="background:#f8fafc;border-radius:10px;border:1px solid #e9ecef">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            System Logo
                        </label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            @if($settings['logo_path'])
                                <img src="{{ asset('storage/' . $settings['logo_path']) }}"
                                     alt="Logo"
                                     style="height:48px;width:auto;border-radius:8px;border:1px solid #e9ecef;padding:4px;background:#fff">
                            @else
                                <div style="width:48px;height:48px;background:#e8f5ee;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#1a7a4a;font-size:1.2rem">
                                    <i class="bi bi-buildings"></i>
                                </div>
                            @endif
                            <div>
                                <div style="font-size:.8rem;font-weight:600;color:#1a1a2e">
                                    {{ $settings['logo_path'] ? 'Logo uploaded' : 'No logo uploaded' }}
                                </div>
                                <div style="font-size:.72rem;color:#6c757d">
                                    Recommended: PNG or SVG, max 2MB
                                </div>
                            </div>
                        </div>
                        <input type="file" name="logo" class="form-control"
                               accept="image/*"
                               style="font-size:.82rem">
                        <small class="text-muted" style="font-size:.72rem">
                            Leave empty to keep current logo. Logo appears in the sidebar and on PDFs.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">System Name</label>
                        <input type="text" name="system_name" class="form-control"
                               value="{{ $settings['system_name'] }}" placeholder="MakaziLink v2">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">Company Name</label>
                        <input type="text" class="form-control"
                            value="{{ $settings['system_name'] }}" disabled
                            style="background:#f8fafc;color:#6c757d">
                        <small class="text-muted" style="font-size:.72rem">This is set automatically from System Name above</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">Company Phone</label>
                        <input type="text" name="company_phone" class="form-control"
                               value="{{ $settings['company_phone'] }}" placeholder="0712345678">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">Company Email</label>
                        <input type="email" name="company_email" class="form-control"
                               value="{{ $settings['company_email'] }}" placeholder="info@company.co.ke">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">Company Address</label>
                        <textarea name="company_address" class="form-control" rows="2"
                                  placeholder="Physical address">{{ $settings['company_address'] }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">Currency</label>
                            <input type="text" name="currency" class="form-control"
                                   value="{{ $settings['currency'] }}" placeholder="KES">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">Timezone</label>
                            <select name="timezone" class="form-select">
                                <option value="Africa/Nairobi" {{ $settings['timezone'] === 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi (EAT)</option>
                                <option value="UTC" {{ $settings['timezone'] === 'UTC' ? 'selected' : '' }}>UTC</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rent Settings --}}
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="width:36px;height:36px;background:#dbeafe;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#1e40af">
                            <i class="bi bi-house"></i>
                        </div>
                        <h3 style="font-size:.95rem;font-weight:700;color:#1a1a2e;margin:0">Rent</h3>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Rent Due Day of Month
                        </label>
                        <input type="number" name="rent_due_day" class="form-control"
                               value="{{ $settings['rent_due_day'] }}" min="1" max="28"
                               placeholder="1">
                        <small class="text-muted" style="font-size:.72rem">Day of the month rent is due (1-28)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Default Notice Period (days)
                        </label>
                        <input type="number" name="default_notice_days" class="form-control"
                               value="{{ $settings['default_notice_days'] }}" min="1"
                               placeholder="30">
                        <small class="text-muted" style="font-size:.72rem">Default days notice required before vacating</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Late Payment Penalty (KES)
                        </label>
                        <input type="number" name="late_payment_penalty" class="form-control"
                               value="{{ $settings['late_payment_penalty'] }}" min="0"
                               placeholder="0">
                        <small class="text-muted" style="font-size:.72rem">Fixed penalty for late rent payment</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Default Water Rate (KES per unit)
                        </label>
                        <input type="number" name="default_water_rate" class="form-control"
                               value="{{ $settings['default_water_rate'] }}" min="0"
                               placeholder="60">
                        <small class="text-muted" style="font-size:.72rem">Default rate used when recording water readings</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Invoice Settings --}}
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="width:36px;height:36px;background:#dcfce7;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#15803d">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <h3 style="font-size:.95rem;font-weight:700;color:#1a1a2e;margin:0">Invoices & Receipts</h3>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">Invoice Prefix</label>
                            <input type="text" name="invoice_prefix" class="form-control"
                                   value="{{ $settings['invoice_prefix'] }}" placeholder="INV">
                            <small class="text-muted" style="font-size:.72rem">e.g. INV-00001</small>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">Receipt Prefix</label>
                            <input type="text" name="receipt_prefix" class="form-control"
                                   value="{{ $settings['receipt_prefix'] }}" placeholder="RCP">
                            <small class="text-muted" style="font-size:.72rem">e.g. RCP-00001</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Invoice Due Days
                        </label>
                        <input type="number" name="invoice_due_days" class="form-control"
                               value="{{ $settings['invoice_due_days'] }}" min="1"
                               placeholder="5">
                        <small class="text-muted" style="font-size:.72rem">Days after invoice creation before it is due</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Default Invoice Notes
                        </label>
                        <textarea name="invoice_notes" class="form-control" rows="3"
                                  placeholder="e.g. Please pay via M-Pesa Paybill 123456">{{ $settings['invoice_notes'] }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notification Settings --}}
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="width:36px;height:36px;background:#fef3c7;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#b45309">
                            <i class="bi bi-bell"></i>
                        </div>
                        <h3 style="font-size:.95rem;font-weight:700;color:#1a1a2e;margin:0">Notifications</h3>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Lease Expiry Alert (days before)
                        </label>
                        <input type="number" name="lease_alert_days" class="form-control"
                               value="{{ $settings['lease_alert_days'] }}" min="1"
                               placeholder="30">
                        <small class="text-muted" style="font-size:.72rem">Show alert when lease expires within this many days</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            WhatsApp Receipts
                        </label>
                        <select name="send_whatsapp" class="form-select">
                            <option value="0" {{ $settings['send_whatsapp'] === '0' ? 'selected' : '' }}>Manual — send only when clicked</option>
                            <option value="1" {{ $settings['send_whatsapp'] === '1' ? 'selected' : '' }}>Prompt after every payment</option>
                        </select>
                        <small class="text-muted" style="font-size:.72rem">When to offer WhatsApp receipt delivery</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- MPesa Settings --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius:12px">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="width:36px;height:36px;background:#dcfce7;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#15803d">
                            <i class="bi bi-phone"></i>
                        </div>
                        <h3 style="font-size:.95rem;font-weight:700;color:#1a1a2e;margin:0">M-Pesa Integration</h3>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-3">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Environment
                            </label>
                            <select name="mpesa_environment" class="form-select">
                                <option value="sandbox" {{ $settings['mpesa_environment'] === 'sandbox' ? 'selected' : '' }}>
                                    Sandbox (Testing)
                                </option>
                                <option value="production" {{ $settings['mpesa_environment'] === 'production' ? 'selected' : '' }}>
                                    Production (Live)
                                </option>
                            </select>
                            <small class="text-muted" style="font-size:.72rem">Use Sandbox for testing</small>
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Shortcode (Till/Paybill)
                            </label>
                            <input type="text" name="mpesa_shortcode" class="form-control"
                                   value="{{ $settings['mpesa_shortcode'] }}"
                                   placeholder="174379">
                            <small class="text-muted" style="font-size:.72rem">Sandbox: 174379</small>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Consumer Key
                            </label>
                            <input type="text" name="mpesa_consumer_key" class="form-control"
                                   value="{{ $settings['mpesa_consumer_key'] }}"
                                   placeholder="Your consumer key">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Consumer Secret
                            </label>
                            <input type="password" name="mpesa_consumer_secret" class="form-control"
                                   value="{{ $settings['mpesa_consumer_secret'] }}"
                                   placeholder="Your consumer secret">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Passkey
                            </label>
                            <input type="password" name="mpesa_passkey" class="form-control"
                                   value="{{ $settings['mpesa_passkey'] }}"
                                   placeholder="Your passkey">
                            <small class="text-muted" style="font-size:.72rem">
                                Sandbox passkey: bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919
                            </small>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                                Callback URL
                            </label>
                            <input type="text" name="mpesa_callback_url" class="form-control"
                                   value="{{ $settings['mpesa_callback_url'] }}"
                                   placeholder="https://yourdomain.com/mpesa/callback">
                            <small class="text-muted" style="font-size:.72rem">
                                URL Safaricom will call after payment. Use ngrok for local testing.
                            </small>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-info mb-0" style="font-size:.8rem;border-radius:8px">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Sandbox Testing:</strong> Use shortcode <strong>174379</strong> and the sandbox passkey above.
                                For the callback URL during local testing, install
                                <a href="https://ngrok.com" target="_blank">ngrok</a>
                                and use your ngrok URL.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- System Info --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius:12px">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="width:36px;height:36px;background:#f3e8ff;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#7e22ce">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <h3 style="font-size:.95rem;font-weight:700;color:#1a1a2e;margin:0">System Information</h3>
                    </div>

                    <div class="row g-3">
                        <div class="col-6 col-md-2">
                            <div style="font-size:.72rem;color:#6c757d;margin-bottom:2px">Version</div>
                            <div style="font-weight:700;color:#1a1a2e">MakaziLink v2</div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div style="font-size:.72rem;color:#6c757d;margin-bottom:2px">Properties</div>
                            <div style="font-weight:700;color:#1a1a2e">{{ $stats['total_properties'] }}</div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div style="font-size:.72rem;color:#6c757d;margin-bottom:2px">Units</div>
                            <div style="font-weight:700;color:#1a1a2e">{{ $stats['total_units'] }}</div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div style="font-size:.72rem;color:#6c757d;margin-bottom:2px">Tenants</div>
                            <div style="font-weight:700;color:#1a1a2e">{{ $stats['total_tenants'] }}</div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div style="font-size:.72rem;color:#6c757d;margin-bottom:2px">Payments</div>
                            <div style="font-weight:700;color:#1a1a2e">{{ $stats['total_payments'] }}</div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div style="font-size:.72rem;color:#6c757d;margin-bottom:2px">Invoices</div>
                            <div style="font-weight:700;color:#1a1a2e">{{ $stats['total_invoices'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="mt-4 d-flex gap-3">
        <button type="submit" class="btn"
                style="background:#1a7a4a;color:#fff;border-radius:8px;padding:10px 28px;font-size:.9rem;font-weight:600;">
            <i class="bi bi-check-lg me-2"></i>Save Settings
        </button>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"
           style="border-radius:8px;padding:10px 20px;font-size:.9rem">
            Cancel
        </a>
    </div>

</form>

@endsection