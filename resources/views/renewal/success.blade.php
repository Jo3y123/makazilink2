<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Renewed — {{ $companyName }}</title>
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
            padding: 40px 20px;
        }
        .renewal-card {
            background: #fff;
            border-radius: 16px;
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 8px 40px rgba(0,0,0,.08);
        }
        @keyframes scaleIn {
            from { transform: scale(0); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #dcfce7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 2.2rem;
            color: #15803d;
            animation: scaleIn .4s ease;
        }
    </style>
</head>
<body>
 
<div class="renewal-card">
 
    <div class="success-icon">
        <i class="bi bi-check-lg"></i>
    </div>
 
    <h1 style="font-size:1.4rem;font-weight:700;color:#1a1a2e;margin-bottom:8px">
        Payment Verified! 🎉
    </h1>
 
    <p style="font-size:.88rem;color:#6c757d;margin-bottom:28px">
        Your subscription has been renewed successfully.
        You can now log in and access all features.
    </p>
 
    {{-- Summary --}}
    <div style="background:#f0fdf4;border-radius:10px;padding:20px;margin-bottom:28px;text-align:left">
        <div style="font-size:.75rem;font-weight:600;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px">
            Renewal Summary
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span style="font-size:.82rem;color:#6c757d">Plan</span>
            <span style="font-size:.82rem;font-weight:600;color:#1a1a2e">{{ ucfirst($plan) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span style="font-size:.82rem;color:#6c757d">Amount Paid</span>
            <span style="font-size:.82rem;font-weight:600;color:#1a1a2e">KES {{ number_format($amount) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span style="font-size:.82rem;color:#6c757d">Days Activated</span>
            <span style="font-size:.82rem;font-weight:600;color:#1a1a2e">{{ $days }} days</span>
        </div>
        <div class="d-flex justify-content-between">
            <span style="font-size:.82rem;color:#6c757d">Expires On</span>
            <span style="font-size:.82rem;font-weight:700;color:#15803d">{{ $expiryDate }}</span>
        </div>
    </div>
 
    <a href="{{ route('login') }}" class="btn w-100"
       style="background:#1a7a4a;color:#fff;border-radius:8px;padding:12px;font-size:.9rem;font-weight:600;text-decoration:none;display:block">
        <i class="bi bi-box-arrow-in-right me-2"></i>Go to Login
    </a>
 
    <div style="margin-top:16px;font-size:.78rem;color:#9ca3af">
        {{ $companyName }} — Thank you for your payment
    </div>
 
</div>
 
</body>
</html>
 