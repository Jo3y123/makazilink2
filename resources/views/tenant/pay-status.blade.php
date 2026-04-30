<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status — {{ \App\Models\Setting::get('system_name', 'MakaziLink v2') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f4f6f8;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
        .content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .status-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            max-width: 440px;
            width: 100%;
            text-align: center;
            box-shadow: 0 8px 40px rgba(0,0,0,.08);
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: .5; }
        }
        @keyframes scaleIn {
            from { transform: scale(0); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>

<header class="portal-header">
    <div class="brand">
        <div class="brand-icon"><i class="bi bi-buildings text-white"></i></div>
        {{ \App\Models\Setting::get('system_name', 'MakaziLink v2') }} Tenant Portal
    </div>
    <a href="{{ route('tenant.portal') }}"
       style="font-size:.82rem;color:rgba(255,255,255,.6);text-decoration:none">
        <i class="bi bi-arrow-left me-1"></i>Back to Portal
    </a>
</header>

<div class="content">
    <div class="status-card">

        {{-- Pending --}}
        <div id="status-pending">
            <div style="width:70px;height:70px;background:#fef3c7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;color:#b45309">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-bottom:8px">
                Check Your Phone
            </h3>
            <p style="font-size:.85rem;color:#6c757d;margin-bottom:8px">
                A payment request has been sent to:
            </p>
            <p style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                {{ $phone ?? 'your phone' }}
            </p>
            <p style="font-size:.82rem;color:#6c757d;margin-bottom:20px">
                Enter your M-Pesa PIN to complete the payment.
                This page updates automatically.
            </p>
            <div class="d-flex justify-content-center mb-3">
                <div style="width:12px;height:12px;background:#1a7a4a;border-radius:50%;margin:0 4px;animation:pulse 1.4s ease-in-out infinite"></div>
                <div style="width:12px;height:12px;background:#1a7a4a;border-radius:50%;margin:0 4px;animation:pulse 1.4s ease-in-out infinite .2s"></div>
                <div style="width:12px;height:12px;background:#1a7a4a;border-radius:50%;margin:0 4px;animation:pulse 1.4s ease-in-out infinite .4s"></div>
            </div>
            <div style="font-size:.78rem;color:#9ca3af">
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
            <p style="font-size:.85rem;color:#6c757d;margin-bottom:8px">
                Your payment of
            </p>
            <p style="font-size:1.3rem;font-weight:700;color:#15803d;margin-bottom:16px">
                KES {{ number_format($amount ?? 0) }}
            </p>
            <p style="font-size:.82rem;color:#6c757d;margin-bottom:24px">
                has been received and recorded successfully.
            </p>
            <a href="{{ route('tenant.portal') }}"
               style="display:block;background:#1a7a4a;color:#fff;border-radius:8px;padding:12px;font-size:.9rem;font-weight:600;text-decoration:none">
                <i class="bi bi-arrow-left me-2"></i>Back to Portal
            </a>
        </div>

        {{-- Cancelled --}}
        <div id="status-cancelled" style="display:none">
            <div style="width:70px;height:70px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;color:#b91c1c">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-bottom:8px">
                Payment Cancelled
            </h3>
            <p id="cancelled-message" style="font-size:.85rem;color:#6c757d;margin-bottom:24px">
                You cancelled the payment request.
            </p>
            <a href="{{ route('tenant.portal') }}"
               style="display:block;background:#1a7a4a;color:#fff;border-radius:8px;padding:12px;font-size:.9rem;font-weight:600;text-decoration:none;margin-bottom:12px">
                <i class="bi bi-arrow-repeat me-2"></i>Try Again
            </a>
            <a href="{{ route('tenant.portal') }}"
               style="display:block;border:1.5px solid #e9ecef;color:#6c757d;border-radius:8px;padding:10px;font-size:.85rem;text-decoration:none">
                Back to Portal
            </a>
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
                Your payment could not be completed.
            </p>
            <a href="{{ route('tenant.portal') }}"
               style="display:block;background:#1a7a4a;color:#fff;border-radius:8px;padding:12px;font-size:.9rem;font-weight:600;text-decoration:none;margin-bottom:12px">
                <i class="bi bi-arrow-repeat me-2"></i>Try Again
            </a>
            <a href="{{ route('tenant.portal') }}"
               style="display:block;border:1.5px solid #e9ecef;color:#6c757d;border-radius:8px;padding:10px;font-size:.85rem;text-decoration:none">
                Back to Portal
            </a>
        </div>

        {{-- Timeout --}}
        <div id="status-timeout" style="display:none">
            <div style="width:70px;height:70px;background:#fef3c7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.8rem;color:#b45309">
                <i class="bi bi-clock-fill"></i>
            </div>
            <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-bottom:8px">
                Request Timed Out
            </h3>
            <p style="font-size:.85rem;color:#6c757d;margin-bottom:24px">
                You did not respond to the payment request in time. Please go back and try again.
            </p>
            <a href="{{ route('tenant.portal') }}"
               style="display:block;background:#1a7a4a;color:#fff;border-radius:8px;padding:12px;font-size:.9rem;font-weight:600;text-decoration:none;margin-bottom:12px">
                <i class="bi bi-arrow-repeat me-2"></i>Try Again
            </a>
            <a href="{{ route('tenant.portal') }}"
               style="display:block;border:1.5px solid #e9ecef;color:#6c757d;border-radius:8px;padding:10px;font-size:.85rem;text-decoration:none">
                Back to Portal
            </a>
        </div>

        {{-- Invoice Info --}}
        @if($invoice)
        <div style="background:#f8fafc;border-radius:8px;padding:12px;margin-top:20px;text-align:left;font-size:.78rem;color:#6c757d">
            Invoice: <strong>{{ $invoice->invoice_number }}</strong> —
            {{ $invoice->unit->property->name }}, Unit {{ $invoice->unit->unit_number }}
        </div>
        @endif

    </div>
</div>

<script>
let pollCount    = 0;
const maxPolls   = 24;
let countdownVal = 5;
let countdownTimer;
let pollTimer;

function showStatus(status, message) {
    ['pending', 'success', 'cancelled', 'failed', 'timeout'].forEach(s => {
        document.getElementById('status-' + s).style.display = 'none';
    });
    document.getElementById('status-' + status).style.display = 'block';

    if (message) {
        const el = document.getElementById('cancelled-message') ||
                   document.getElementById('failed-message');
        if (el) el.textContent = message;
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
        if (countdownVal <= 0) clearInterval(countdownTimer);
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
            } else if (data.status === 'cancelled') {
                showStatus('cancelled', data.message);
            } else if (data.status === 'timeout') {
                showStatus('timeout');
            } else if (data.status === 'failed') {
                showStatus('failed', data.message);
            } else {
                startCountdown();
                pollTimer = setTimeout(checkStatus, 5000);
            }
        })
        .catch(() => {
            startCountdown();
            pollTimer = setTimeout(checkStatus, 5000);
        });
}

startCountdown();
pollTimer = setTimeout(checkStatus, 5000);
</script>

</body>
</html>