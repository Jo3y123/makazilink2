@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)
@section('page-title', 'Invoice Details')

@section('content')

<div class="d-flex justify-content-center">
    <div style="width:100%;max-width:680px">

        <div class="d-flex align-items-center gap-3 mb-3">
            <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">
                    {{ $invoice->invoice_number }}
                </h2>
                <p class="text-muted mb-0" style="font-size:.8rem">
                    {{ $invoice->tenant->user->name }} — {{ $invoice->unit->unit_number }}
                </p>
            </div>
        </div>

        <div class="d-flex gap-2 mb-4 flex-wrap">
            <a href="{{ route('invoices.pdf', $invoice) }}"
            class="btn btn-sm btn-outline-secondary"
            style="border-radius:8px;font-size:.82rem">
                <i class="bi bi-file-pdf me-1"></i>Download PDF
            </a>
            <a href="{{ route('payments.create') }}?invoice_id={{ $invoice->id }}"
            class="btn btn-sm btn-outline-secondary"
            style="border-radius:8px;font-size:.82rem">
                <i class="bi bi-cash-coin me-1"></i>Record Payment
            </a>
            @if($invoice->balance > 0)
            <a href="{{ route('mpesa.push', $invoice) }}"
            class="btn btn-sm"
            style="background:#15803d;color:#fff;border-radius:8px;font-size:.82rem;padding:6px 16px">
                <i class="bi bi-phone me-1"></i>Request M-Pesa
            </a>
            @endif
        </div>

        {{-- Invoice Card --}}
        <div class="card border-0 shadow-sm mb-3" style="border-radius:12px">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <div style="font-size:1.3rem;font-weight:700;color:#1a1a2e">INVOICE</div>
                        <div style="font-size:.82rem;color:#6c757d">{{ $invoice->invoice_number }}</div>
                    </div>
                    <div class="text-end">
                        @if($invoice->status === 'paid')
                            <span class="badge" style="background:#dcfce7;color:#15803d;border-radius:20px;font-size:.8rem;padding:6px 14px">PAID</span>
                        @elseif($invoice->status === 'partial')
                            <span class="badge" style="background:#dbeafe;color:#1e40af;border-radius:20px;font-size:.8rem;padding:6px 14px">PARTIAL</span>
                        @elseif($invoice->status === 'overdue')
                            <span class="badge" style="background:#fee2e2;color:#b91c1c;border-radius:20px;font-size:.8rem;padding:6px 14px">OVERDUE</span>
                        @else
                            <span class="badge" style="background:#f1f5f9;color:#64748b;border-radius:20px;font-size:.8rem;padding:6px 14px">DRAFT</span>
                        @endif
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div style="font-size:.7rem;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Billed To</div>
                        <div style="font-weight:700;color:#1a1a2e">{{ $invoice->tenant->user->name }}</div>
                        <div style="font-size:.82rem;color:#6c757d">{{ $invoice->tenant->user->email }}</div>
                        <div style="font-size:.82rem;color:#6c757d">{{ $invoice->tenant->user->phone }}</div>
                    </div>
                    <div class="col-6 text-end">
                        <div style="font-size:.7rem;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Period</div>
                        <div style="font-size:.85rem;color:#1a1a2e">
                            {{ $invoice->period_start->format('d M Y') }} —
                            {{ $invoice->period_end->format('d M Y') }}
                        </div>
                        <div style="font-size:.7rem;color:#6c757d;margin-top:8px">Due Date</div>
                        <div style="font-size:.85rem;font-weight:600;color:#1a1a2e">
                            {{ $invoice->due_date->format('d M Y') }}
                        </div>
                    </div>
                </div>

                <div class="p-3 mb-4" style="background:#f8fafc;border-radius:8px">
                    <div style="font-size:.78rem;color:#6c757d">
                        <i class="bi bi-building me-1"></i>{{ $invoice->unit->property->name }} —
                        Unit {{ $invoice->unit->unit_number }},
                        {{ $invoice->unit->property->address }}
                    </div>
                </div>

                <table class="table table-sm mb-0">
                    <thead style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d">
                        <tr>
                            <th style="border:none;padding:8px 0">Description</th>
                            <th style="border:none;padding:8px 0;text-align:right">Amount (KES)</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:.85rem">
                        <tr>
                            <td style="padding:8px 0;border-color:#f0f0f0">Rent</td>
                            <td style="padding:8px 0;border-color:#f0f0f0;text-align:right">{{ number_format($invoice->rent_amount) }}</td>
                        </tr>
                        @if($invoice->water_amount > 0)
                        <tr>
                            <td style="padding:8px 0;border-color:#f0f0f0">Water</td>
                            <td style="padding:8px 0;border-color:#f0f0f0;text-align:right">{{ number_format($invoice->water_amount) }}</td>
                        </tr>
                        @endif
                        @if($invoice->garbage_amount > 0)
                        <tr>
                            <td style="padding:8px 0;border-color:#f0f0f0">Garbage</td>
                            <td style="padding:8px 0;border-color:#f0f0f0;text-align:right">{{ number_format($invoice->garbage_amount) }}</td>
                        </tr>
                        @endif
                        @if($invoice->other_amount > 0)
                        <tr>
                            <td style="padding:8px 0;border-color:#f0f0f0">Other</td>
                            <td style="padding:8px 0;border-color:#f0f0f0;text-align:right">{{ number_format($invoice->other_amount) }}</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td style="padding:12px 0;font-weight:700;border-top:2px solid #e9ecef">Total</td>
                            <td style="padding:12px 0;font-weight:700;border-top:2px solid #e9ecef;text-align:right">
                                {{ number_format($invoice->total_amount) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:4px 0;color:#15803d;border:none">Amount Paid</td>
                            <td style="padding:4px 0;color:#15803d;border:none;text-align:right">
                                {{ number_format($invoice->amount_paid) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:4px 0;font-weight:700;color:#b91c1c;border:none">Balance Due</td>
                            <td style="padding:4px 0;font-weight:700;color:#b91c1c;border:none;text-align:right">
                                {{ number_format($invoice->balance) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($invoice->payments->count() > 0)
        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <p style="font-size:.8rem;font-weight:700;color:#1a1a2e;margin-bottom:12px">
                    Payment History
                </p>
                <table class="table table-sm mb-0">
                    <thead style="font-size:.72rem;text-transform:uppercase;color:#6c757d">
                        <tr>
                            <th style="border:none">Receipt</th>
                            <th style="border:none">Date</th>
                            <th style="border:none">Method</th>
                            <th style="border:none;text-align:right">Amount</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:.82rem">
                        @foreach($invoice->payments as $payment)
                        <tr>
                            <td style="border-color:#f0f0f0">
                                <a href="{{ route('payments.show', $payment) }}"
                                   style="color:#1a7a4a;font-weight:600">
                                    {{ $payment->receipt_number }}
                                </a>
                            </td>
                            <td style="border-color:#f0f0f0">{{ $payment->payment_date->format('d M Y') }}</td>
                            <td style="border-color:#f0f0f0">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            <td style="border-color:#f0f0f0;text-align:right;font-weight:600">
                                {{ number_format($payment->amount) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</div>

@endsection