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
            max-width: 560px;
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
        .step.active { background: #1a7a4a; color: #fff; }
        .step.inactive { background: #e9ecef; color: #6c757d; }
        .step-line { flex: 1; height: 2px; background: #e9ecef; }
        .plan-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 16px;
            cursor: pointer;
            transition: border-color .15s, background .15s;
            margin-bottom: 12px;
        }
        .plan-card:hover { border-color: #1a7a4a; background: #f0fdf4; }
        .plan-card input[type="radio"] { display: none; }
        .plan-card.selected { border-color: #1a7a4a; background: #f0fdf4; }
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
            {{ $companyName }} — Choose a plan to continue
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
        Step 1 of 3 — Choose Your Plan
    </p>
 
    @if($subscription)
    <div style="background:#fef3c7;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:.82rem;color:#b45309">
        <i class="bi bi-info-circle me-2"></i>
        Your current plan is <strong>{{ $subscription->planLabel() }}</strong>.
        @if($subscription->expires_at)
            It expired on <strong>{{ $subscription->expires_at->format('d M Y') }}</strong>.
        @endif
    </div>
    @endif
 
    <form action="{{ route('renew.instructions') }}" method="POST">
        @csrf
 
        {{-- Plan Cards --}}
        <div class="mb-4">
            <label class="plan-card" id="card-starter" onclick="selectPlan('starter')">
                <input type="radio" name="plan" value="starter" checked>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:.9rem;font-weight:700;color:#1a1a2e">Starter</div>
                        <div style="font-size:.78rem;color:#6c757d">Up to 20 units</div>
                    </div>
                    <div style="font-size:1.1rem;font-weight:700;color:#1a7a4a">KES 2,500<span style="font-size:.7rem;font-weight:400;color:#6c757d">/mo</span></div>
                </div>
            </label>
 
            <label class="plan-card" id="card-growth" onclick="selectPlan('growth')">
                <input type="radio" name="plan" value="growth">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:.9rem;font-weight:700;color:#1a1a2e">Growth</div>
                        <div style="font-size:.78rem;color:#6c757d">Up to 50 units</div>
                    </div>
                    <div style="font-size:1.1rem;font-weight:700;color:#1a7a4a">KES 5,000<span style="font-size:.7rem;font-weight:400;color:#6c757d">/mo</span></div>
                </div>
            </label>
 
            <label class="plan-card" id="card-pro" onclick="selectPlan('pro')">
                <input type="radio" name="plan" value="pro">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:.9rem;font-weight:700;color:#1a1a2e">Pro</div>
                        <div style="font-size:.78rem;color:#6c757d">Up to 100 units</div>
                    </div>
                    <div style="font-size:1.1rem;font-weight:700;color:#1a7a4a">KES 8,000<span style="font-size:.7rem;font-weight:400;color:#6c757d">/mo</span></div>
                </div>
            </label>
 
            <label class="plan-card" id="card-enterprise" onclick="selectPlan('enterprise')">
                <input type="radio" name="plan" value="enterprise">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:.9rem;font-weight:700;color:#1a1a2e">Enterprise</div>
                        <div style="font-size:.78rem;color:#6c757d">Unlimited units</div>
                    </div>
                    <div style="font-size:1.1rem;font-weight:700;color:#1a7a4a">KES 15,000<span style="font-size:.7rem;font-weight:400;color:#6c757d">/mo</span></div>
                </div>
            </label>
        </div>
 
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
 
<script>
function selectPlan(plan) {
    document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
    document.getElementById('card-' + plan).classList.add('selected');
    document.querySelector('input[value="' + plan + '"]').checked = true;
}
// Select starter by default
selectPlan('starter');
</script>
 
</body>
</html>
 