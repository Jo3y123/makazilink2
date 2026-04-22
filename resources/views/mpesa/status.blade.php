@extends('layouts.app')

@section('title', 'Payment Status')
@section('page-title', 'Payment Status')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-5">

        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4 text-center">

                <div id="status-pending">
                    <div style="width:70px;height:70px;background:#fef3c7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;color:#b45309">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-bottom:8px">
                        Waiting for Payment
                    </h3>
                    <p style="font-size:.85rem;color:#6c757d;margin-bottom:24px">
                        A payment request has been sent to the tenant's phone.
                        Ask them to check their phone and enter their M-Pesa PIN.
                    </p>
                    <div class="d-flex justify-content-center mb-4">
                        <div style="width:12px;height:12px;background:#1a7a4a;border-radius:50%;margin:0 4px;animation:pulse 1.4s ease-in-out infinite"></div>
                        <div style="width:12px;height:12px;background:#1a7a4a;border-radius:50%;margin:0 4px;animation:pulse 1.4s ease-in-out infinite .2s"></div>
                        <div style="width:12px;height:12px;background:#1a7a4a;border-radius:50%;margin:0 4px;animation:pulse 1.4s ease-in-out infinite .4s"></div>
                    </div>
                </div>

                <div id="status-success" style="display:none">
                    <div style="width:70px;height:70px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;color:#15803d">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-bottom:8px">
                        Payment Successful!
                    </h3>
                    <p style="font-size:.85rem;color:#6c757d;margin-bottom:24px">
                        The payment has been confirmed and recorded automatically.
                    </p>
                </div>

                <div id="status-cancelled" style="display:none">
                    <div style="width:70px;height:70px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;color:#b91c1c">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-bottom:8px">
                        Payment Cancelled
                    </h3>
                    <p style="font-size:.85rem;color:#6c757d;margin-bottom:24px">
                        The tenant cancelled the payment request.
                    </p>
                </div>

                @if($invoice)
                <div class="p-3 mb-4" style="background:#f8fafc;border-radius:8px;text-align:left">
                    <div style="font-size:.78rem;color:#6c757d">
                        <strong>{{ $invoice->tenant->user->name }}</strong> —
                        {{ $invoice->unit->unit_number }}, {{ $invoice->unit->property->name }}<br>
                        Invoice: {{ $invoice->invoice_number }} —
                        Balance: KES {{ number_format($invoice->balance) }}
                    </div>
                </div>
                @endif

                <div class="d-flex gap-2 justify-content-center">
                    @if($invoice)
                    <a href="{{ route('invoices.show', $invoice) }}"
                       class="btn btn-sm btn-outline-secondary"
                       style="border-radius:8px;font-size:.82rem">
                        View Invoice
                    </a>
                    @endif
                    <a href="{{ route('payments.index') }}"
                       class="btn btn-sm"
                       style="background:#1a7a4a;color:#fff;border-radius:8px;font-size:.82rem">
                        View Payments
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
</style>
@endpush

@push('scripts')
<script>
// Poll payment status every 5 seconds
let pollCount = 0;
const maxPolls = 24; // 2 minutes

function checkStatus() {
    if (pollCount >= maxPolls) return;
    pollCount++;

    fetch('{{ route("mpesa.query") }}')
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('status-pending').style.display   = 'none';
                document.getElementById('status-success').style.display   = 'block';
                document.getElementById('status-cancelled').style.display = 'none';
            } else if (data.status === 'cancelled') {
                document.getElementById('status-pending').style.display   = 'none';
                document.getElementById('status-success').style.display   = 'none';
                document.getElementById('status-cancelled').style.display = 'block';
            } else {
                setTimeout(checkStatus, 5000);
            }
        })
        .catch(() => {
            setTimeout(checkStatus, 5000);
        });
}

// Start polling after 5 seconds
setTimeout(checkStatus, 5000);
</script>
@endpush

@endsection