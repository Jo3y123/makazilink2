@extends('layouts.app')

@section('title', 'Receipt ' . $payment->receipt_number)
@section('page-title', 'Payment Receipt')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-7">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">
                    Receipt {{ $payment->receipt_number }}
                </h2>
                <p class="text-muted mb-0" style="font-size:.8rem">
                    {{ $payment->tenant->user->name }}
                </p>
            </div>
        </div>

        {{-- Receipt Card --}}
        <div class="card border-0 shadow-sm" style="border-radius:12px" id="receipt">
            <div class="card-body p-4">

                {{-- Brand --}}
                <div class="d-flex align-items-center gap-3 mb-4 pb-3"
                     style="border-bottom:1px solid #f0f0f0">
                    <div style="width:40px;height:40px;background:#0f2d1e;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem">
                        <i class="bi bi-buildings"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;color:#1a1a2e">MakaziLink v2</div>
                        <div style="font-size:.72rem;color:#6c757d">Official Payment Receipt</div>
                    </div>
                    <div class="ms-auto text-end">
                        <div style="font-size:1rem;font-weight:700;color:#1a7a4a">RECEIPT</div>
                        <div style="font-size:.78rem;color:#6c757d">{{ $payment->receipt_number }}</div>
                    </div>
                </div>

                {{-- Details --}}
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div style="font-size:.7rem;color:#6c757d;margin-bottom:2px">Received From</div>
                        <div style="font-weight:700;color:#1a1a2e">{{ $payment->tenant->user->name }}</div>
                        <div style="font-size:.78rem;color:#6c757d">{{ $payment->tenant->user->phone }}</div>
                    </div>
                    <div class="col-6 text-end">
                        <div style="font-size:.7rem;color:#6c757d;margin-bottom:2px">Payment Date</div>
                        <div style="font-weight:700;color:#1a1a2e">{{ $payment->payment_date->format('d M Y') }}</div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div style="font-size:.7rem;color:#6c757d;margin-bottom:2px">Unit</div>
                        <div style="font-weight:600;color:#1a1a2e">{{ $payment->unit->unit_number }}</div>
                        <div style="font-size:.78rem;color:#6c757d">{{ $payment->unit->property->name }}</div>
                    </div>
                    <div class="col-6 text-end">
                        <div style="font-size:.7rem;color:#6c757d;margin-bottom:2px">Payment Method</div>
                        <div style="font-weight:600;color:#1a1a2e">
                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                        </div>
                        @if($payment->mpesa_transaction_id)
                            <div style="font-size:.75rem;color:#6c757d">{{ $payment->mpesa_transaction_id }}</div>
                        @endif
                        @if($payment->reference_number)
                            <div style="font-size:.75rem;color:#6c757d">Ref: {{ $payment->reference_number }}</div>
                        @endif
                    </div>
                </div>

                @if($payment->invoice)
                <div class="p-3 mb-4" style="background:#f8fafc;border-radius:8px">
                    <div style="font-size:.78rem;color:#6c757d">
                        <i class="bi bi-receipt me-1"></i>Invoice: {{ $payment->invoice->invoice_number }}
                    </div>
                </div>
                @endif

                {{-- Amount --}}
                <div class="p-4 text-center mb-4"
                     style="background:#0f2d1e;border-radius:10px">
                    <div style="font-size:.75rem;color:rgba(255,255,255,.5);margin-bottom:4px;text-transform:uppercase;letter-spacing:.05em">
                        Amount Paid
                    </div>
                    <div style="font-size:2rem;font-weight:700;color:#fff">
                        KES {{ number_format($payment->amount) }}
                    </div>
                </div>

                @if($payment->notes)
                <div class="mb-4">
                    <div style="font-size:.72rem;color:#6c757d;margin-bottom:4px">Notes</div>
                    <div style="font-size:.82rem;color:#374151">{{ $payment->notes }}</div>
                </div>
                @endif

                {{-- Footer --}}
                <div class="text-center pt-3" style="border-top:1px solid #f0f0f0">
                    <div style="font-size:.72rem;color:#6c757d">
                        Recorded by {{ $payment->recordedBy->name }} •
                        {{ $payment->created_at->format('d M Y, h:i A') }}
                    </div>
                    <div style="font-size:.7rem;color:#9ca3af;margin-top:4px">
                        MakaziLink v2 — Rental Management System
                    </div>
                </div>

            </div>
        </div>

        <div class="d-flex gap-2 justify-content-center mt-3 flex-wrap">
            <a href="{{ route('payments.pdf', $payment) }}"
            class="btn btn-sm btn-outline-secondary"
            style="border-radius:8px;font-size:.82rem">
                <i class="bi bi-file-pdf me-1"></i>Download PDF
            </a>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"
                    style="border-radius:8px;font-size:.82rem">
                <i class="bi bi-printer me-1"></i>Print Receipt
            </button>
            <a href="{{ route('payments.whatsapp', $payment) }}"
            class="btn btn-sm"
            style="background:#25d366;color:#fff;border-radius:8px;font-size:.82rem"
            target="_blank">
                <i class="bi bi-whatsapp me-1"></i>Send via WhatsApp
            </a>
        </div>

    </div>
</div>

@endsection