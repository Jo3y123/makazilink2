@extends('layouts.app')

@section('title', 'Bulk WhatsApp Reminders')
@section('page-title', 'Bulk WhatsApp Reminders')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary"
       style="border-radius:8px">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">Bulk WhatsApp Reminders</h2>
        <p class="text-muted mb-0" style="font-size:.8rem">
            {{ count($messages) }} tenant(s) with unpaid invoices
        </p>
    </div>
</div>

@if(empty($messages))
    <div class="text-center py-5">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-whatsapp"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">All tenants are up to date!</h3>
        <p class="text-muted" style="font-size:.82rem">No unpaid invoices found.</p>
    </div>
@else
    <div class="alert alert-info mb-4" style="font-size:.82rem;border-radius:8px">
        <i class="bi bi-info-circle me-2"></i>
        Click <strong>Send</strong> on each tenant to open WhatsApp with the reminder pre-filled.
        The message will be ready to send — just press Send in WhatsApp.
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:12px">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d">
                    <tr>
                        <th class="px-4 py-3">Tenant</th>
                        <th class="py-3">Phone</th>
                        <th class="py-3">Balance Due</th>
                        <th class="py-3">Action</th>
                    </tr>
                </thead>
                <tbody style="font-size:.85rem">
                    @foreach($messages as $msg)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:34px;height:34px;background:#e8f5ee;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#1a7a4a;flex-shrink:0">
                                    {{ strtoupper(substr($msg['name'], 0, 2)) }}
                                </div>
                                <div style="font-weight:600;color:#1a1a2e">{{ $msg['name'] }}</div>
                            </div>
                        </td>
                        <td class="py-3">{{ $msg['phone'] }}</td>
                        <td class="py-3">
                            <span style="font-weight:700;color:#b91c1c">
                                KES {{ number_format($msg['balance']) }}
                            </span>
                        </td>
                        <td class="py-3">
                            <a href="{{ $msg['url'] }}" target="_blank"
                               class="btn btn-sm"
                               style="background:#25d366;color:#fff;border-radius:8px;font-size:.78rem;padding:5px 14px">
                                <i class="bi bi-whatsapp me-1"></i>Send Reminder
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection