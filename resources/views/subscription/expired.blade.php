<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Expired</title>
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
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .expired-card {
            background: #fff;
            border-radius: 16px;
            padding: 48px 40px;
            max-width: 480px;
            width: 90%;
            text-align: center;
            box-shadow: 0 8px 40px rgba(0,0,0,.08);
        }
        .expired-icon {
            width: 72px;
            height: 72px;
            background: #fee2e2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 2rem;
            color: #b91c1c;
        }
    </style>
</head>
<body>

<div class="expired-card">
    <div class="expired-icon">
        <i class="bi bi-lock"></i>
    </div>

    <h1 style="font-size:1.4rem;font-weight:700;color:#1a1a2e;margin-bottom:8px">
        Subscription Expired
    </h1>

    <p style="font-size:.88rem;color:#6c757d;margin-bottom:24px">
        Your access to {{ $companyName }} has expired.
        Please renew your subscription to continue.
    </p>

    @if($subscription)
    <div style="background:#f8fafc;border-radius:10px;padding:16px;margin-bottom:24px;text-align:left">
        <div style="font-size:.75rem;font-weight:600;color:#6c757d;text-transform:uppercase;margin-bottom:12px">
            Subscription Details
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span style="font-size:.82rem;color:#6c757d">Plan</span>
            <span style="font-size:.82rem;font-weight:600;color:#1a1a2e">{{ $subscription->planLabel() }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span style="font-size:.82rem;color:#6c757d">Status</span>
            <span style="font-size:.82rem;font-weight:600;color:#b91c1c">{{ ucfirst($subscription->status) }}</span>
        </div>
        @if($subscription->expires_at)
        <div class="d-flex justify-content-between">
            <span style="font-size:.82rem;color:#6c757d">Expired On</span>
            <span style="font-size:.82rem;font-weight:600;color:#1a1a2e">
                {{ $subscription->expires_at->format('d M Y') }}
            </span>
        </div>
        @endif
    </div>
    @endif

    <div style="background:#f0fdf4;border-radius:10px;padding:16px;margin-bottom:24px;text-align:left">
        <div style="font-size:.75rem;font-weight:600;color:#15803d;text-transform:uppercase;margin-bottom:8px">
            How to Renew
        </div>
        <p style="font-size:.82rem;color:#374151;margin:0">
            Send your renewal payment via M-Pesa and contact us with your confirmation.
            @if($phone)
                <br><br>
                <strong>Contact:</strong> {{ $phone }}
            @endif
        </p>
    </div>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
                style="background:#1a7a4a;color:#fff;border:none;border-radius:8px;padding:10px 24px;font-size:.88rem;font-weight:600;cursor:pointer;width:100%">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
        </button>
    </form>
</div>

</body>
</html>
