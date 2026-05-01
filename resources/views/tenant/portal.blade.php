<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Portal — MakaziLink v2</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f4f6f8;
            margin: 0;
        }
        .portal-header {
            background: #0f2d1e;
            color: #fff;
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .portal-header .brand {
            font-weight: 700;
            font-size: .95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .portal-header .brand-icon {
            width: 30px;
            height: 30px;
            background: #1a7a4a;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .8rem;
        }
        .portal-content {
            max-width: 760px;
            margin: 32px auto;
            padding: 0 20px;
        }
        .portal-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            padding: 24px;
            margin-bottom: 16px;
        }
        .card-title {
            font-size: .88rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card-title i {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .9rem;
        }
        .stat-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }
        @media (min-width: 576px) {
            .stat-row { grid-template-columns: repeat(4, 1fr); }
        }
        .mini-stat {
            background: #f8fafc;
            border-radius: 10px;
            padding: 14px;
            border: 1px solid #e9ecef;
            text-align: center;
        }
        .mini-stat .s-label {
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6c757d;
            font-weight: 600;
        }
        .mini-stat .s-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-top: 4px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: .83rem;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #6c757d; }
        .info-value { font-weight: 600; color: #1a1a2e; }
        .badge-paid    { background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600; }
        .badge-partial { background:#dbeafe;color:#1e40af;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600; }
        .badge-overdue { background:#fee2e2;color:#b91c1c;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600; }
        .badge-draft   { background:#f1f5f9;color:#64748b;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600; }
        .badge-open    { background:#fef3c7;color:#b45309;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600; }
        .badge-resolved{ background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600; }
        .empty-state {
            text-align: center;
            padding: 20px 0;
            color: #9ca3af;
            font-size: .82rem;
        }
    </style>
</head>
<body>
 
<header class="portal-header">
    <div class="brand">
        <div class="brand-icon"><i class="bi bi-buildings text-white"></i></div>
        {{ \App\Models\Setting::get('system_name', 'MakaziLink v2') }} Tenant Portal
    </div>
    <div class="d-flex align-items-center gap-3">
        <span style="font-size:.82rem;opacity:.7">{{ $user->name }}</span>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit"
                    style="background:none;border:none;color:rgba(255,255,255,.5);font-size:.8rem;cursor:pointer;padding:0">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </button>
        </form>
    </div>
</header>
 
<div class="portal-content">
 
    {{-- Welcome --}}
    <div class="mb-4">
        <h1 style="font-size:1.2rem;font-weight:700;color:#1a1a2e;margin-bottom:4px">
            Hello, {{ $user->name }} 👋
        </h1>
        <p class="text-muted mb-0" style="font-size:.82rem">
            {{ now()->format('l, d F Y') }}
        </p>
    </div>
 
    @if(!$tenant)
    {{-- No tenant profile --}}
    <div class="portal-card text-center py-5">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-person-x"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">Tenant Profile Not Set Up</h3>
        <p class="text-muted" style="font-size:.82rem">
            Your landlord has not yet set up your tenant profile. Please contact them to get started.
        </p>
    </div>
    @else
 
    {{-- Messages Button --}}
    <a href="{{ route('tenant.messages') }}"
       class="d-flex align-items-center gap-3 mb-4 p-3"
       style="background:#fff;border-radius:12px;border:1px solid #e9ecef;text-decoration:none;color:inherit">
        <div style="width:40px;height:40px;background:#e8f5ee;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#1a7a4a;font-size:1.1rem;flex-shrink:0">
            <i class="bi bi-chat-dots"></i>
        </div>
        <div>
            <div style="font-size:.88rem;font-weight:700;color:#1a1a2e">Messages</div>
            <div style="font-size:.75rem;color:#6c757d">Send a message to your landlord or caretaker</div>
        </div>
        <i class="bi bi-chevron-right ms-auto" style="color:#9ca3af"></i>
    </a>

    {{-- Summary Stats --}}
    <div class="stat-row">
        <div class="mini-stat">
            <div class="s-label">Balance Due</div>
            <div class="s-value" style="color:{{ $totalBalance > 0 ? '#b91c1c' : '#15803d' }}">
                KES {{ number_format($totalBalance) }}
            </div>
        </div>
        <div class="mini-stat">
            <div class="s-label">Total Paid</div>
            <div class="s-value" style="color:#15803d">KES {{ number_format($totalPaid) }}</div>
        </div>
        <div class="mini-stat">
            <div class="s-label">Unpaid Invoices</div>
            <div class="s-value" style="color:{{ $unpaidInvoices > 0 ? '#b91c1c' : '#15803d' }}">
                {{ $unpaidInvoices }}
            </div>
        </div>
        <div class="mini-stat">
            <div class="s-label">Unit</div>
            <div class="s-value" style="font-size:.9rem">
                {{ $lease ? $lease->unit->unit_number : '—' }}
            </div>
        </div>
    </div>
 
    {{-- Lease Details --}}
    @if($lease)
    <div class="portal-card">
        <div class="card-title">
            <i class="bi bi-file-earmark-text" style="background:#e8f5ee;color:#1a7a4a"></i>
            My Lease
        </div>
        <div class="info-row">
            <span class="info-label">Property</span>
            <span class="info-value">{{ $lease->unit->property->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Unit</span>
            <span class="info-value">{{ $lease->unit->unit_number }} — {{ ucfirst(str_replace('_', ' ', $lease->unit->type)) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Address</span>
            <span class="info-value">{{ $lease->unit->property->address }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Monthly Rent</span>
            <span class="info-value" style="color:#1a7a4a">KES {{ number_format($lease->monthly_rent) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Deposit Paid</span>
            <span class="info-value">KES {{ number_format($lease->deposit_paid) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Lease Start</span>
            <span class="info-value">{{ $lease->start_date->format('d M Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Lease End</span>
            <span class="info-value">{{ $lease->end_date ? $lease->end_date->format('d M Y') : 'Open Ended' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Notice Period</span>
            <span class="info-value">{{ $lease->notice_days }} days</span>
        </div>
        <div class="info-row">
            <span class="info-label">Next Due Date</span>
            <span class="info-value" style="color:#b45309">
                {{ $lease->next_due_date ? $lease->next_due_date->format('d M Y') : '—' }}
            </span>
        </div>
    </div>
    @endif
 
    {{-- Invoices --}}
    <div class="portal-card">
        <div class="card-title">
            <i class="bi bi-receipt" style="background:#dbeafe;color:#1e40af"></i>
            My Invoices
        </div>
        @if($invoices->isEmpty())
            <div class="empty-state"><i class="bi bi-receipt fs-4 d-block mb-2"></i>No invoices yet</div>
        @else
            @foreach($invoices as $invoice)
            <div class="info-row" style="flex-wrap:wrap;gap:8px">
                <div style="flex:1;min-width:0">
                    <div style="font-size:.83rem;font-weight:600;color:#1a1a2e">{{ $invoice->invoice_number }}</div>
                    <div style="font-size:.75rem;color:#6c757d">
                        {{ $invoice->period_start->format('d M Y') }} — {{ $invoice->period_end->format('d M Y') }}
                        · Due {{ $invoice->due_date->format('d M Y') }}
                    </div>
                </div>
                <div class="text-end">
                    <div style="font-size:.83rem;font-weight:700;color:#1a1a2e">KES {{ number_format($invoice->total_amount) }}</div>
                    @if($invoice->status === 'paid')
                        <span class="badge-paid">Paid</span>
                    @elseif($invoice->status === 'partial')
                        <span class="badge-partial">Partial · KES {{ number_format($invoice->balance) }} left</span>
                    @elseif($invoice->status === 'overdue')
                        <span class="badge-overdue">Overdue</span>
                    @else
                        <span class="badge-draft">{{ ucfirst($invoice->status) }}</span>
                    @endif
                </div>
                @if($invoice->balance > 0)
                <div style="width:100%">
                    <button onclick="showPayModal({{ $invoice->id }}, {{ $invoice->balance }})"
                            style="width:100%;background:#1a7a4a;color:#fff;border:none;border-radius:8px;padding:8px;font-size:.82rem;font-weight:600;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif">
                        <i class="bi bi-phone me-1"></i>Pay KES {{ number_format($invoice->balance) }} via M-Pesa
                    </button>
                </div>
                @endif
            </div>
            @endforeach
        @endif
    </div>
 
    {{-- Payments --}}
    <div class="portal-card">
        <div class="card-title">
            <i class="bi bi-cash-coin" style="background:#dcfce7;color:#15803d"></i>
            My Payments
        </div>
        @if($payments->isEmpty())
            <div class="empty-state"><i class="bi bi-cash-coin fs-4 d-block mb-2"></i>No payments recorded yet</div>
        @else
            @foreach($payments as $payment)
            <div class="info-row">
                <div>
                    <div style="font-size:.83rem;font-weight:600;color:#1a1a2e">{{ $payment->receipt_number }}</div>
                    <div style="font-size:.75rem;color:#6c757d">
                        {{ $payment->payment_date->format('d M Y') }}
                        · {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                        @if($payment->mpesa_transaction_id)
                            · {{ $payment->mpesa_transaction_id }}
                        @endif
                    </div>
                </div>
                <div style="font-size:.83rem;font-weight:700;color:#15803d">
                    KES {{ number_format($payment->amount) }}
                </div>
            </div>
            @endforeach
        @endif
    </div>
 
    {{-- Maintenance --}}
    <div class="portal-card">
        <div class="card-title">
            <i class="bi bi-tools" style="background:#fff7ed;color:#c2410c"></i>
            My Maintenance Requests
        </div>

        @if(session('success'))
            <div style="background:#dcfce7;border-radius:8px;padding:10px 14px;margin-bottom:12px;font-size:.82rem;color:#15803d">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background:#fee2e2;border-radius:8px;padding:10px 14px;margin-bottom:12px;font-size:.82rem;color:#b91c1c">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        {{-- Report Issue Button --}}
        <button onclick="document.getElementById('maintenanceForm').style.display='block';this.style.display='none'"
                style="width:100%;background:#fff7ed;border:1.5px solid #fed7aa;color:#c2410c;border-radius:8px;padding:10px;font-size:.85rem;font-weight:600;cursor:pointer;margin-bottom:16px;font-family:'Plus Jakarta Sans',sans-serif">
            <i class="bi bi-plus-circle me-2"></i>Report a Maintenance Issue
        </button>

        {{-- Maintenance Form --}}
        <div id="maintenanceForm" style="display:none;background:#fff7ed;border-radius:10px;padding:16px;margin-bottom:16px;border:1px solid #fed7aa">
            <div style="font-size:.85rem;font-weight:700;color:#c2410c;margin-bottom:12px">
                <i class="bi bi-tools me-2"></i>Report an Issue
            </div>
            <form action="{{ route('tenant.maintenance.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label style="font-size:.78rem;font-weight:600;color:#374151;display:block;margin-bottom:4px">
                        Issue Title <span style="color:#b91c1c">*</span>
                    </label>
                    <input type="text" name="title" class="form-control"
                           placeholder="e.g. Leaking pipe in bathroom"
                           style="font-size:.82rem;border-radius:8px" required>
                </div>
                <div class="mb-3">
                    <label style="font-size:.78rem;font-weight:600;color:#374151;display:block;margin-bottom:4px">
                        Description
                    </label>
                    <textarea name="description" class="form-control" rows="3"
                              placeholder="Describe the issue in detail..."
                              style="font-size:.82rem;border-radius:8px"></textarea>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label style="font-size:.78rem;font-weight:600;color:#374151;display:block;margin-bottom:4px">
                            Category <span style="color:#b91c1c">*</span>
                        </label>
                        <select name="category" class="form-select"
                                style="font-size:.82rem;border-radius:8px" required>
                            <option value="plumbing">Plumbing</option>
                            <option value="electrical">Electrical</option>
                            <option value="general" selected>General</option>
                            <option value="structural">Structural</option>
                            <option value="appliance">Appliance</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label style="font-size:.78rem;font-weight:600;color:#374151;display:block;margin-bottom:4px">
                            Priority <span style="color:#b91c1c">*</span>
                        </label>
                        <select name="priority" class="form-select"
                                style="font-size:.82rem;border-radius:8px" required>
                            <option value="low">Low</option>
                            <option value="normal" selected>Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit"
                            style="background:#c2410c;color:#fff;border:none;border-radius:8px;padding:9px 20px;font-size:.85rem;font-weight:600;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif">
                        <i class="bi bi-send me-1"></i>Submit Request
                    </button>
                    <button type="button"
                            onclick="document.getElementById('maintenanceForm').style.display='none';document.querySelector('[onclick*=maintenanceForm]').style.display='block'"
                            style="background:none;border:1.5px solid #e9ecef;border-radius:8px;padding:9px 16px;font-size:.85rem;color:#6c757d;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        @if($maintenanceRequests->isEmpty())
            <div class="empty-state"><i class="bi bi-tools fs-4 d-block mb-2"></i>No maintenance requests submitted</div>
        @else
            @foreach($maintenanceRequests as $req)
            <div class="info-row">
                <div>
                    <div style="font-size:.83rem;font-weight:600;color:#1a1a2e">{{ $req->title }}</div>
                    <div style="font-size:.75rem;color:#6c757d">
                        {{ $req->created_at->format('d M Y') }}
                        · {{ ucfirst(str_replace('_', ' ', $req->category ?? 'general')) }}
                        · {{ ucfirst($req->priority) }} priority
                    </div>
                </div>
                @if(in_array($req->status, ['resolved', 'closed']))
                    <span class="badge-resolved">{{ ucfirst($req->status) }}</span>
                @else
                    <span class="badge-open">{{ ucfirst(str_replace('_', ' ', $req->status)) }}</span>
                @endif
            </div>
            @endforeach
        @endif
    </div>
 
    {{-- Contact --}}
    @if($lease)
    <div class="portal-card">
        <div class="card-title">
            <i class="bi bi-telephone" style="background:#f3e8ff;color:#7e22ce"></i>
            Contact Landlord
        </div>
        <div class="info-row">
            <span class="info-label">Name</span>
            <span class="info-value">{{ $lease->unit->property->owner->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Phone</span>
            <span class="info-value">
                <a href="tel:{{ $lease->unit->property->owner->phone }}"
                   style="color:#1a7a4a">
                    {{ $lease->unit->property->owner->phone }}
                </a>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Email</span>
            <span class="info-value">
                <a href="mailto:{{ $lease->unit->property->owner->email }}"
                   style="color:#1a7a4a">
                    {{ $lease->unit->property->owner->email }}
                </a>
            </span>
        </div>
    </div>
    @endif
 
    @endif
 
</div>
 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
{{-- Pay Modal --}}
<div id="payModal"
     style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.5);z-index:9000;align-items:center;justify-content:center"
     onclick="if(event.target===this) closePayModal()">
    <div style="background:#fff;border-radius:16px;width:90%;max-width:400px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.2)">
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-bottom:8px">
            <i class="bi bi-phone me-2 text-success"></i>Pay via M-Pesa
        </h3>
        <p style="font-size:.82rem;color:#6c757d;margin-bottom:16px">
            Enter your M-Pesa number. You will receive a prompt to enter your PIN.
        </p>
        <form action="{{ route('tenant.pay') }}" method="POST">
            @csrf
            <input type="hidden" name="invoice_id" id="modal_invoice_id">
            <div class="mb-3">
                <label style="font-size:.8rem;font-weight:600;color:#374151;display:block;margin-bottom:6px">
                    Amount
                </label>
                <div id="modal_amount_display"
                     style="font-size:1.1rem;font-weight:700;color:#1a7a4a;margin-bottom:12px">
                </div>
                <label style="font-size:.8rem;font-weight:600;color:#374151;display:block;margin-bottom:6px">
                    M-Pesa Phone Number <span style="color:#b91c1c">*</span>
                </label>
                <input type="text" name="phone"
                       class="form-control"
                       placeholder="e.g. 0712345678"
                       value="{{ $user->phone }}"
                       style="border-radius:8px;font-size:.85rem"
                       required>
                <small style="font-size:.72rem;color:#6c757d">
                    Make sure this is your M-Pesa registered number
                </small>
            </div>
            <button type="submit"
                    style="width:100%;background:#1a7a4a;color:#fff;border:none;border-radius:8px;padding:12px;font-size:.9rem;font-weight:600;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;margin-bottom:8px">
                <i class="bi bi-send me-2"></i>Send Payment Request
            </button>
            <button type="button" onclick="closePayModal()"
                    style="width:100%;background:none;border:1.5px solid #e9ecef;border-radius:8px;padding:10px;font-size:.85rem;color:#6c757d;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif">
                Cancel
            </button>
        </form>
    </div>
</div>

<script>
function showPayModal(invoiceId, amount) {
    document.getElementById('modal_invoice_id').value = invoiceId;
    document.getElementById('modal_amount_display').textContent = 'KES ' + amount.toLocaleString();
    document.getElementById('payModal').style.display = 'flex';
}

function closePayModal() {
    document.getElementById('payModal').style.display = 'none';
}
</script>
</body>
</html>
 