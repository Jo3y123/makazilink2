@extends('layouts.app')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">All Payments</h2>
        <p class="text-muted mb-0" style="font-size:.82rem">Payment history and receipts</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('payments.bulk.whatsapp') }}" class="btn btn-sm"
           style="background:#25d366;color:#fff;border-radius:8px;padding:8px 16px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-whatsapp me-1"></i> Send Reminders
        </a>
        <a href="{{ route('payments.create') }}" class="btn btn-sm"
           style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-plus-lg me-1"></i> Record Payment
        </a>
    </div>
</div>

@if($payments->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-cash-coin"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">No payments yet</h3>
        <p class="text-muted" style="font-size:.82rem">Click Record Payment to add the first payment</p>
        <a href="{{ route('payments.create') }}" class="btn btn-sm"
           style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-plus-lg me-1"></i> Record Payment
        </a>
    </div>
@else
    <div class="card border-0 shadow-sm" style="border-radius:12px">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d">
                    <tr>
                        <th class="px-4 py-3">Receipt #</th>
                        <th class="py-3">Tenant</th>
                        <th class="py-3">Unit</th>
                        <th class="py-3">Amount (KES)</th>
                        <th class="py-3">Method</th>
                        <th class="py-3">Date</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody style="font-size:.85rem">
                    @foreach($payments as $payment)
                    <tr>
                        <td class="px-4 py-3">
                            <span style="font-weight:700;color:#1a7a4a">{{ $payment->receipt_number }}</span>
                        </td>
                        <td class="py-3">{{ $payment->tenant->user->name }}</td>
                        <td class="py-3">
                            {{ $payment->unit->unit_number }}
                            <span class="text-muted" style="font-size:.75rem"> — {{ $payment->unit->property->name }}</span>
                        </td>
                        <td class="py-3">
                            <span style="font-weight:700;color:#15803d">{{ number_format($payment->amount) }}</span>
                        </td>
                        <td class="py-3">
                            @if($payment->payment_method === 'mpesa')
                                <span class="badge" style="background:#dcfce7;color:#15803d;border-radius:20px;font-size:.7rem">M-Pesa</span>
                            @elseif($payment->payment_method === 'cash')
                                <span class="badge" style="background:#fef3c7;color:#b45309;border-radius:20px;font-size:.7rem">Cash</span>
                            @elseif($payment->payment_method === 'bank_transfer')
                                <span class="badge" style="background:#dbeafe;color:#1e40af;border-radius:20px;font-size:.7rem">Bank</span>
                            @else
                                <span class="badge" style="background:#f3e8ff;color:#7e22ce;border-radius:20px;font-size:.7rem">Cheque</span>
                            @endif
                        </td>
                        <td class="py-3">{{ $payment->payment_date->format('d M Y') }}</td>
                        <td class="py-3">
                            <div class="d-flex gap-2">
                                <a href="{{ route('payments.show', $payment) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('payments.destroy', $payment) }}" method="POST"
                                      onsubmit="return confirm('Delete this payment record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection