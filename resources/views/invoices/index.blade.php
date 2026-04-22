@extends('layouts.app')
 
@section('title', 'Invoices')
@section('page-title', 'Invoices')
 
@section('content')
 
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">All Invoices</h2>
        <p class="text-muted mb-0" style="font-size:.82rem">Manage tenant invoices</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-secondary"
                data-bs-toggle="modal" data-bs-target="#bulkModal"
                style="border-radius:8px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-lightning me-1"></i> Bulk Generate
        </button>
        <a href="{{ route('invoices.create') }}" class="btn btn-sm"
           style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-plus-lg me-1"></i> New Invoice
        </a>
    </div>
</div>
 
{{-- Bulk Generate Modal --}}
<div class="modal fade" id="bulkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0">
                <h5 class="modal-title" style="font-size:.95rem;font-weight:700">
                    <i class="bi bi-lightning me-2 text-warning"></i>Bulk Generate Invoices
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('invoices.bulk') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
 
                    <div class="alert alert-info mb-4" style="font-size:.82rem;border-radius:8px">
                        <i class="bi bi-info-circle me-2"></i>
                        This will create invoices for <strong>all active leases</strong> for the selected period.
                        Tenants who already have an invoice for that period will be skipped.
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Billing Period <span class="text-danger">*</span>
                        </label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="date" name="period_start" class="form-control"
                                       value="{{ now()->startOfMonth()->format('Y-m-d') }}" required>
                                <small class="text-muted" style="font-size:.72rem">Period Start</small>
                            </div>
                            <div class="col-6">
                                <input type="date" name="period_end" class="form-control"
                                       value="{{ now()->endOfMonth()->format('Y-m-d') }}" required>
                                <small class="text-muted" style="font-size:.72rem">Period End</small>
                            </div>
                        </div>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Due Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="due_date" class="form-control"
                               value="{{ now()->addDays((int) \App\Models\Setting::get('invoice_due_days', 5))->format('Y-m-d') }}"
                               required>
                        <small class="text-muted" style="font-size:.72rem">
                            Default is {{ (int) \App\Models\Setting::get('invoice_due_days', 5) }} days from today (set in Settings)
                        </small>
                    </div>
 
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm"
                            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:6px 20px;font-size:.85rem;font-weight:600;">
                        <i class="bi bi-lightning me-1"></i> Generate All Invoices
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 
@if($invoices->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-receipt"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">No invoices yet</h3>
        <p class="text-muted" style="font-size:.82rem">Click New Invoice to generate your first invoice</p>
        <a href="{{ route('invoices.create') }}" class="btn btn-sm"
           style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-plus-lg me-1"></i> New Invoice
        </a>
    </div>
@else
    <div class="card border-0 shadow-sm" style="border-radius:12px">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d">
                    <tr>
                        <th class="px-4 py-3">Invoice #</th>
                        <th class="py-3">Tenant</th>
                        <th class="py-3">Unit</th>
                        <th class="py-3">Total (KES)</th>
                        <th class="py-3">Balance (KES)</th>
                        <th class="py-3">Due Date</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody style="font-size:.85rem">
                    @foreach($invoices as $invoice)
                    <tr>
                        <td class="px-4 py-3">
                            <span style="font-weight:700;color:#1a1a2e">{{ $invoice->invoice_number }}</span>
                        </td>
                        <td class="py-3">{{ $invoice->tenant->user->name }}</td>
                        <td class="py-3">
                            {{ $invoice->unit->unit_number }}
                            <span class="text-muted" style="font-size:.75rem"> — {{ $invoice->unit->property->name }}</span>
                        </td>
                        <td class="py-3">{{ number_format($invoice->total_amount) }}</td>
                        <td class="py-3">
                            <span style="color:{{ $invoice->balance > 0 ? '#b91c1c' : '#15803d' }};font-weight:600">
                                {{ number_format($invoice->balance) }}
                            </span>
                        </td>
                        <td class="py-3">{{ $invoice->due_date->format('d M Y') }}</td>
                        <td class="py-3">
                            @if($invoice->status === 'paid')
                                <span class="badge" style="background:#dcfce7;color:#15803d;border-radius:20px;font-size:.7rem">Paid</span>
                            @elseif($invoice->status === 'partial')
                                <span class="badge" style="background:#dbeafe;color:#1e40af;border-radius:20px;font-size:.7rem">Partial</span>
                            @elseif($invoice->status === 'overdue')
                                <span class="badge" style="background:#fee2e2;color:#b91c1c;border-radius:20px;font-size:.7rem">Overdue</span>
                            @elseif($invoice->status === 'sent')
                                <span class="badge" style="background:#f3e8ff;color:#7e22ce;border-radius:20px;font-size:.7rem">Sent</span>
                            @else
                                <span class="badge" style="background:#f1f5f9;color:#64748b;border-radius:20px;font-size:.7rem">Draft</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <div class="d-flex gap-2">
                                <a href="{{ route('invoices.show', $invoice) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST"
                                      onsubmit="return confirm('Delete invoice {{ $invoice->invoice_number }}?')">
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
 