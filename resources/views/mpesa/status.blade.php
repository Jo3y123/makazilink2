@extends('layouts.app')
 
@section('title', 'Payment Status')
@section('page-title', 'Payment Status')
 
@section('content')
 
<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-5">
 
        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4 text-center">
 
                {{-- Pending --}}
                <div id="status-pending">
                    <div style="width:70px;height:70px;background:#fef3c7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;color:#b45309">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-bottom:8px">
                        Waiting for Payment
                    </h3>
                    <p style="font-size:.85rem;color:#6c757d;margin-bottom:8px">
                        A payment request has been sent to:
                    </p>
                    <p style="font-size:.95rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                        {{ $phone ?? 'tenant phone' }}
                    </p>
                    <p style="font-size:.82rem;color:#6c757d;margin-bottom:20px">
                        Ask the tenant to check their phone and enter their M-Pesa PIN.
                        This page updates automatically.
                    </p>
                    <div class="d-flex justify-content-center mb-4">
                        <div style="width:12px;height:12px;background:#1a7a4a;border-radius:50%;margin:0 4px;animation:pulse 1.4s ease-in-out infinite"></div>
                        <div style="width:12px;height:12px;background:#1a7a4a;border-radius:50%;margin:0 4px;animation:pulse 1.4s ease-in-out infinite .2s"></div>
                        <div style="width:12px;height:12px;background:#1a7a4a;border-radius:50%;margin:0 4px;animation:pulse 1.4s ease-in-out infinite .4s"></div>
                    </div>
                    <div id="timer" style="font-size:.78rem;color:#9ca3af">
                        Checking in <span id="countdown">5</span> seconds...
                    </div>
                </div>
 
                {{-- Success --}}
                <div id="status-success" style="display:none">
                    <div style="width:70px;height:70px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;color:#15803d;animation:scaleIn .3s ease">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-bottom:8px">
                        Payment Successful! ✅
                    </h3>
                    <p style="font-size:.85rem;color:#6c757d;margin-bottom:24px">
                        Payment confirmed and recorded automatically.
                        A receipt has been prepared for the tenant.
                    </p>
                </div>
 
                {{-- Cancelled --}}
                <div id="status-cancelled" style="display:none">
                    <div style="width:70px;height:70px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;color:#b91c1c">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-bottom:8px">
                        Payment Cancelled
                    </h3>
                    <p id="failure-message" style="font-size:.85rem;color:#6c757d;margin-bottom:24px">
                        The tenant cancelled the payment request.
                    </p>
                    @if($invoice)
                    <a href="{{ route('mpesa.push', $invoice) }}"
                       class="btn btn-sm mb-3"
                       style="background:#1a7a4a;color:#fff;border-radius:8px;font-size:.85rem;padding:8px 20px">
                        <i class="bi bi-arrow-repeat me-1"></i> Resend Payment Request
                    </a>
                    @endif
                </div>
 
                {{-- Failed --}}
                <div id="status-failed" style="display:none">
                    <div style="width:70px;height:70px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;color:#b91c1c">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-bottom:8px">
                        Payment Failed
                    </h3>
                    <p id="failed-message" style="font-size:.85rem;color:#6c757d;margin-bottom:24px">
                        The payment could not be completed.
                    </p>
                    @if($invoice)
                    <a href="{{ route('mpesa.push', $invoice) }}"
                       class="btn btn-sm mb-3"
                       style="background:#1a7a4a;color:#fff;border-radius:8px;font-size:.85rem;padding:8px 20px">
                        <i class="bi bi-arrow-repeat me-1"></i> Try Again
                    </a>
                    @endif
                </div>
 
                {{-- Timeout --}}
                <div id="status-timeout" style="display:none">
                    <div style="width:70px;height:70px;background:#fef3c7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;color:#b45309">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                    <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-bottom:8px">
                        Payment Timed Out
                    </h3>
                    <p style="font-size:.85rem;color:#6c757d;margin-bottom:24px">
                        The tenant did not respond to the payment request within the allowed time.
                        You can resend the request.
                    </p>
                    @if($invoice)
                    <a href="{{ route('mpesa.push', $invoice) }}"
                       class="btn btn-sm mb-3"
                       style="background:#1a7a4a;color:#fff;border-radius:8px;font-size:.85rem;padding:8px 20px">
                        <i class="bi bi-arrow-repeat me-1"></i> Resend Payment Request
                    </a>
                    @endif
                </div>
 
                {{-- Invoice Info --}}
                @if($invoice)
                <div class="p-3 mb-4" style="background:#f8fafc;border-radius:8px;text-align:left">
                    <div style="font-size:.78rem;color:#6c757d">
                        <strong>{{ $invoice->tenant->user->name }}</strong> —
                        Unit {{ $invoice->unit->unit_number }}, {{ $invoice->unit->property->name }}<br>
                        Invoice: {{ $invoice->invoice_number }} —
                        Amount: KES {{ number_format($amount ?? $invoice->balance) }}
                    </div>
                </div>
                @endif
 
                {{-- Action Buttons --}}
                <div class="d-flex gap-2 justify-content-center">
                    @if($invoice)
                    <a href="{{ route('invoices.show', $invoice) }}"
                       class="btn btn-sm btn-outline-secondary"
                       style="border-radius:8px;font-size:.82rem">
                        <i class="bi bi-receipt me-1"></i>View Invoice
                    </a>
                    @endif
                    <a href="{{ route('payments.index') }}"
                       class="btn btn-sm"
                       style="background:#1a7a4a;color:#fff;border-radius:8px;font-size:.82rem">
                        <i class="bi bi-cash-coin me-1"></i>View Payments
                    </a>
                </div>
 
            </div>
        </div>
 
    </div>
</div>
 
@push('styles')
<style>
@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.3); opacity: .5; }
}
@keyframes scaleIn {
    from { transform: scale(0); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
}
</style>
@endpush
 
@push('scripts')
<script>
let pollCount     = 0;
const maxPolls    = 24; // 2 minutes total (24 x 5 seconds)
let countdownVal  = 5;
let countdownTimer;
let pollTimer;
 
function showStatus(status, message) {
    // Hide all
    ['pending', 'success', 'cancelled', 'failed', 'timeout'].forEach(s => {
        document.getElementById('status-' + s).style.display = 'none';
    });
 
    // Show target
    document.getElementById('status-' + status).style.display = 'block';
 
    // Set message if provided
    if (message) {
        const msgEl = document.getElementById('failure-message') ||
                      document.getElementById('failed-message');
        if (msgEl) msgEl.textContent = message;
    }
}
 
function startCountdown() {
    clearInterval(countdownTimer);
    countdownVal = 5;
    const el = document.getElementById('countdown');
    if (el) el.textContent = countdownVal;
 
    countdownTimer = setInterval(() => {
        countdownVal--;
        if (el) el.textContent = countdownVal;
        if (countdownVal <= 0) {
            clearInterval(countdownTimer);
        }
    }, 1000);
}
 
function checkStatus() {
    if (pollCount >= maxPolls) {
        showStatus('timeout');
        return;
    }
 
    pollCount++;
 
    fetch('{{ route("mpesa.query") }}')
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showStatus('success');
                clearTimeout(pollTimer);
            } else if (data.status === 'cancelled') {
                showStatus('cancelled', data.message);
                clearTimeout(pollTimer);
            } else if (data.status === 'timeout') {
                showStatus('timeout');
                clearTimeout(pollTimer);
            } else if (data.status === 'failed') {
                showStatus('failed', data.message);
                clearTimeout(pollTimer);
            } else {
                // Still pending — poll again
                startCountdown();
                pollTimer = setTimeout(checkStatus, 5000);
            }
        })
        .catch(() => {
            startCountdown();
            pollTimer = setTimeout(checkStatus, 5000);
        });
}
 
// Start first poll after 5 seconds
startCountdown();
pollTimer = setTimeout(checkStatus, 5000);
</script>
@endpush
 
@endsection
 