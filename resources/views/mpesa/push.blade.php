@extends('layouts.app')

@section('title', 'Request M-Pesa Payment')
@section('page-title', 'Request M-Pesa Payment')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-5">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">Request M-Pesa Payment</h2>
                <p class="text-muted mb-0" style="font-size:.8rem">{{ $invoice->invoice_number }} — {{ $invoice->tenant->user->name }}</p>
            </div>
        </div>

        {{-- Invoice Summary --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius:12px">
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div style="font-size:.72rem;color:#6c757d;margin-bottom:2px">Tenant</div>
                        <div style="font-weight:700;color:#1a1a2e">{{ $invoice->tenant->user->name }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:.72rem;color:#6c757d;margin-bottom:2px">Unit</div>
                        <div style="font-weight:700;color:#1a1a2e">{{ $invoice->unit->unit_number }} — {{ $invoice->unit->property->name }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:.72rem;color:#6c757d;margin-bottom:2px">Invoice Total</div>
                        <div style="font-weight:700;color:#1a1a2e">KES {{ number_format($invoice->total_amount) }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:.72rem;color:#6c757d;margin-bottom:2px">Balance Due</div>
                        <div style="font-weight:700;color:#b91c1c">KES {{ number_format($invoice->balance) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STK Push Form --}}
        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">

                <div class="text-center mb-4">
                    <div style="width:60px;height:60px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:1.5rem;color:#15803d">
                        <i class="bi bi-phone"></i>
                    </div>
                    <p style="font-size:.85rem;color:#374151;margin:0">
                        Enter the tenant's M-Pesa phone number. They will receive a payment prompt on their phone.
                    </p>
                </div>

                <form action="{{ route('mpesa.send') }}" method="POST">
                    @csrf
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Phone Number <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-phone"></i>
                            </span>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ $invoice->tenant->user->phone }}"
                                   placeholder="07XXXXXXXX" required>
                        </div>
                        <small class="text-muted" style="font-size:.72rem">
                            Format: 07XXXXXXXX or 2547XXXXXXXX
                        </small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Amount (KES) <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="amount" class="form-control"
                               value="{{ $invoice->balance }}"
                               min="1" required>
                        <small class="text-muted" style="font-size:.72rem">
                            Default is the outstanding balance
                        </small>
                    </div>

                    <div class="alert alert-warning mb-4" style="font-size:.8rem;border-radius:8px">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Sandbox Mode:</strong> This will not charge real money.
                        Use test phone number <strong>254708374149</strong> for sandbox testing.
                    </div>

                    <button type="submit" class="btn w-100"
                            style="background:#15803d;color:#fff;border-radius:8px;padding:12px;font-size:.9rem;font-weight:600;">
                        <i class="bi bi-phone me-2"></i>Send Payment Request
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection