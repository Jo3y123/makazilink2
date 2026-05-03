<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renew Subscription — {{ $companyName }}</title>
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
            margin: 0;
            padding: 40px 20px;
        }
        .renewal-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            max-width: 520px;
            width: 100%;
            margin: 0 auto;
            box-shadow: 0 8px 40px rgba(0,0,0,.08);
        }
        .step-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 28px;
        }
        .step {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
            font-weight: 700;
        }
        .step.active   { background: #1a7a4a; color: #fff; }
        .step.inactive { background: #e9ecef; color: #6c757d; }
        .step-line     { flex: 1; height: 2px; background: #e9ecef; }
    </style>
</head>
<body>

<div class="renewal-card">

    {{-- Header --}}
    <div style="text-align:center;margin-bottom:28px">
        <div style="width:52px;height:52px;background:#e8f5ee;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:1.4rem;color:#1a7a4a">
            <i class="bi bi-arrow-repeat"></i>
        </div>
        <h1 style="font-size:1.3rem;font-weight:700;color:#1a1a2e;margin-bottom:4px">
            Renew Your Subscription
        </h1>
        <p style="font-size:.85rem;color:#6c757d;margin:0">
            {{ $companyName }}
        </p>
    </div>

    {{-- Step Indicator --}}
    <div class="step-indicator">
        <div class="step active">1</div>
        <div class="step-line"></div>
        <div class="step inactive">2</div>
        <div class="step-line"></div>
        <div class="step inactive">3</div>
    </div>
    <p style="font-size:.8rem;font-weight:600;color:#1a1a2e;margin-bottom:16px">
        Step 1 of 3 — Confirm Your Payment
    </p>

    {{-- Expiry notice --}}
    @if($subscription && $subscription->expires_at)
    <div style="background:#fef3c7;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:.82rem;color:#b45309">
        <i class="bi bi-info-circle me-2"></i>
        Your subscription expired on <strong>{{ $subscription->expires_at->format('d M Y') }}</strong>.
        Renew now to restore access.
    </div>
    @endif

    {{-- Fee Breakdown --}}
    <div style="background:#f0fdf4;border-radius:12px;padding:20px;margin-bottom:24px;border:2px solid #86efac">
        <div style="font-size:.75rem;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px;font-weight:600">
            Your Monthly Fee
        </div>
        <div style="font-size:2rem;font-weight:700;color:#1a7a4a;margin-bottom:4px">
            KES {{ number_format($calculatedFee) }}
        </div>
        <div style="font-size:.82rem;color:#6c757d">
            Based on {{ $tenantCount }} active tenant{{ $tenantCount !== 1 ? 's' : '' }} × KES 100 per tenant
        </div>
        @if($tenantCount === 0)
        <div style="font-size:.78rem;color:#b45309;margin-top:8px">
            <i class="bi bi-exclamation-triangle me-1"></i>
            You have no tenants yet. Minimum fee applies when tenants are added.
        </div>
        @endif
    </div>

    {{-- How fee works --}}
    <div style="background:#f8fafc;border-radius:8px;padding:14px 16px;margin-bottom:24px;font-size:.8rem;color:#6c757d">
        <i class="bi bi-info-circle me-2"></i>
        Your fee updates automatically as your tenant count changes.
        More tenants = higher fee. Fewer tenants = lower fee.
    </div>

    {{-- Continue button --}}
    <form action="{{ route('renew.instructions') }}" method="POST">
        @csrf
        <input type="hidden" name="plan" value="tenant">
        <input type="hidden" name="calculated_fee" value="{{ $calculatedFee }}">
        <button type="submit" class="btn w-100"
                style="background:#1a7a4a;color:#fff;border-radius:8px;padding:12px;font-size:.9rem;font-weight:600">
            Continue to Payment <i class="bi bi-arrow-right ms-2"></i>
        </button>
    </form>

    <div style="text-align:center;margin-top:20px">
        <a href="{{ route('login') }}" style="font-size:.8rem;color:#6c757d;text-decoration:none">
            <i class="bi bi-arrow-left me-1"></i>Back to Login
        </a>
    </div>

</div>

</body>
</html>