<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Instructions — {{ $companyName }}</title>
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
        .step.done { background: #1a7a4a; color: #fff; }
        .step.active { background: #1a7a4a; color: #fff; }
        .step.inactive { background: #e9ecef; color: #6c757d; }
        .step-line { flex: 1; height: 2px; background: #e9ecef; }
        .step-line.done { background: #1a7a4a; }
        .instruction-step {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .instruction-step:last-child { border-bottom: none; }
        .step-num {
            width: 28px;
            height: 28px;
            background: #1a7a4a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }
        .copy-btn {
            background: none;
            border: 1.5px solid #1a7a4a;
            color: #1a7a4a;
            border-radius: 6px;
            padding: 2px 10px;
            font-size: .72rem;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 600;
        }
        .copy-btn:hover { background: #1a7a4a; color: #fff; }
    </style>
</head>
<body>
 
<div class="renewal-card">
 
    {{-- Header --}}
    <div style="text-align:center;margin-bottom:28px">
        <div style="width:52px;height:52px;background:#e8f5ee;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:1.4rem;color:#1a7a4a">
            <i class="bi bi-phone"></i>
        </div>
        <h1 style="font-size:1.3rem;font-weight:700;color:#1a1a2e;margin-bottom:4px">
            Make Payment
        </h1>
        <p style="font-size:.85rem;color:#6c757d;margin:0">
            Follow these steps to complete your payment
        </p>
    </div>
 
    {{-- Step Indicator --}}
    <div class="step-indicator">
        <div class="step done"><i class="bi bi-check" style="font-size:.8rem"></i></div>
        <div class="step-line done"></div>
        <div class="step active">2</div>
        <div class="step-line"></div>
        <div class="step inactive">3</div>
    </div>
    <p style="font-size:.8rem;font-weight:600;color:#1a1a2e;margin-bottom:16px">
        Step 2 of 3 — Pay via M-Pesa
    </p>
 
    {{-- Plan Summary --}}
    <div style="background:#f0fdf4;border-radius:10px;padding:14px 16px;margin-bottom:24px">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div style="font-size:.75rem;color:#6c757d;text-transform:uppercase;letter-spacing:.05em">Selected Plan</div>
                <div style="font-size:.95rem;font-weight:700;color:#1a1a2e">{{ ucfirst($plan) }}</div>
            </div>
            <div style="font-size:1.2rem;font-weight:700;color:#1a7a4a">
                KES {{ number_format($amount) }}
            </div>
        </div>
    </div>
 
    {{-- Payment Instructions --}}
    <div style="border:1.5px solid #e9ecef;border-radius:10px;padding:16px;margin-bottom:24px">
        <div style="font-size:.82rem;font-weight:700;color:#1a1a2e;margin-bottom:12px">
            <i class="bi bi-list-ol me-2 text-success"></i>M-Pesa Payment Steps
        </div>
 
        <div class="instruction-step">
            <div class="step-num">1</div>
            <div style="font-size:.85rem;color:#374151">Open <strong>M-Pesa</strong> on your phone</div>
        </div>
 
        <div class="instruction-step">
            <div class="step-num">2</div>
            <div style="font-size:.85rem;color:#374151">Select <strong>Lipa na M-Pesa</strong></div>
        </div>
 
        <div class="instruction-step">
            <div class="step-num">3</div>
            <div style="font-size:.85rem;color:#374151">Select <strong>Pay Bill</strong></div>
        </div>
 
        <div class="instruction-step">
            <div class="step-num">4</div>
            <div style="font-size:.85rem;color:#374151;flex:1">
                Enter Business No:
                <strong id="paybill">{{ $paybill }}</strong>
                <button class="copy-btn ms-2" onclick="copyText('{{ $paybill }}', this)">Copy</button>
            </div>
        </div>
 
        <div class="instruction-step">
            <div class="step-num">5</div>
            <div style="font-size:.85rem;color:#374151;flex:1">
                Enter Account No:
                <strong id="account">{{ $account }}</strong>
                <button class="copy-btn ms-2" onclick="copyText('{{ $account }}', this)">Copy</button>
            </div>
        </div>
 
        <div class="instruction-step">
            <div class="step-num">6</div>
            <div style="font-size:.85rem;color:#374151;flex:1">
                Enter Amount:
                <strong id="amount">{{ number_format($amount) }}</strong>
                <button class="copy-btn ms-2" onclick="copyText('{{ $amount }}', this)">Copy</button>
            </div>
        </div>
 
        <div class="instruction-step">
            <div class="step-num">7</div>
            <div style="font-size:.85rem;color:#374151">Enter your <strong>M-Pesa PIN</strong> and confirm</div>
        </div>
 
        <div class="instruction-step">
            <div class="step-num">8</div>
            <div style="font-size:.85rem;color:#374151">Wait for the <strong>confirmation SMS</strong> from M-Pesa</div>
        </div>
    </div>
 
    {{-- Warning --}}
    <div style="background:#fef3c7;border-radius:8px;padding:12px 16px;margin-bottom:24px">
        <div style="font-size:.8rem;color:#b45309;font-weight:600;margin-bottom:4px">
            <i class="bi bi-exclamation-triangle me-2"></i>Important
        </div>
        <ul style="font-size:.78rem;color:#92400e;margin:0;padding-left:18px">
            <li>Pay exactly <strong>KES {{ number_format($amount) }}</strong> — not more, not less</li>
            <li>Do not close this page until you receive the SMS</li>
            <li>Keep the M-Pesa confirmation SMS — you will need the code</li>
        </ul>
    </div>
 
    {{-- Continue Button --}}
    <form action="{{ route('renew.verify') }}" method="POST" id="continueForm">
        @csrf
        <input type="hidden" name="plan" value="{{ $plan }}">
        <input type="hidden" name="mpesa_code" id="hidden_code" value="">
        <input type="hidden" name="phone_number" id="hidden_phone" value="">
    </form>
 
    <button type="button" onclick="showVerifyForm()"
            class="btn w-100"
            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:12px;font-size:.9rem;font-weight:600">
        I Have Paid — Enter Confirmation Code <i class="bi bi-arrow-right ms-2"></i>
    </button>
 
    {{-- Verify Form (hidden initially) --}}
    <div id="verifyForm" style="display:none;margin-top:20px">
        <div style="border-top:2px solid #f0f0f0;padding-top:20px">
            <p style="font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px">
                Enter your M-Pesa confirmation details:
            </p>
 
            <div class="mb-3">
                <label style="font-size:.8rem;font-weight:600;color:#374151;display:block;margin-bottom:6px">
                    M-Pesa Confirmation Code <span style="color:#b91c1c">*</span>
                </label>
                <input type="text" id="mpesa_code"
                       class="form-control"
                       placeholder="e.g. QGH7K8L9M2"
                       maxlength="15"
                       oninput="formatCode(this)"
                       style="font-size:.85rem;border-radius:8px;letter-spacing:.05em;text-transform:uppercase">
                <small style="font-size:.72rem;color:#6c757d">
                    Found in the SMS from M-Pesa e.g. <em>"QGH7K8L9M2 Confirmed. KES {{ number_format($amount) }}..."</em>
                </small>
                @error('mpesa_code')
                    <div style="color:#b91c1c;font-size:.78rem;margin-top:4px">{{ $message }}</div>
                @enderror
            </div>
 
            <div class="mb-4">
                <label style="font-size:.8rem;font-weight:600;color:#374151;display:block;margin-bottom:6px">
                    Phone Number Used to Pay <span style="color:#b91c1c">*</span>
                </label>
                <input type="text" id="phone_number"
                       class="form-control"
                       placeholder="e.g. 0712345678"
                       maxlength="15"
                       oninput="formatPhone(this)"
                       style="font-size:.85rem;border-radius:8px">
                <small style="font-size:.72rem;color:#6c757d">
                    The M-Pesa number you used to make the payment
                </small>
                @error('phone_number')
                    <div style="color:#b91c1c;font-size:.78rem;margin-top:4px">{{ $message }}</div>
                @enderror
            </div>
 
            <button type="button" onclick="submitVerify()"
                    class="btn w-100"
                    style="background:#1a7a4a;color:#fff;border-radius:8px;padding:12px;font-size:.9rem;font-weight:600">
                <i class="bi bi-shield-check me-2"></i>Verify Payment
            </button>
        </div>
    </div>
 
    <div style="text-align:center;margin-top:16px">
        <a href="{{ route('renew.index') }}" style="font-size:.8rem;color:#6c757d;text-decoration:none">
            <i class="bi bi-arrow-left me-1"></i>Back to Plans
        </a>
    </div>
 
</div>
 
<script>
function showVerifyForm() {
    document.getElementById('verifyForm').style.display = 'block';
    document.getElementById('mpesa_code').focus();
    document.querySelector('[onclick="showVerifyForm()"]').style.display = 'none';
}
 
function formatCode(input) {
    input.value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
}
 
function formatPhone(input) {
    let val = input.value.replace(/\D/g, '');
    if (val.startsWith('254')) val = '0' + val.slice(3);
    if (val.startsWith('7') || val.startsWith('1')) val = '0' + val;
    input.value = val;
}
 
function submitVerify() {
    const code  = document.getElementById('mpesa_code').value.trim();
    const phone = document.getElementById('phone_number').value.trim();
 
    if (code.length < 8) {
        alert('Please enter a valid M-Pesa code (at least 8 characters)');
        return;
    }
 
    if (phone.length < 10) {
        alert('Please enter a valid phone number');
        return;
    }
 
    document.getElementById('hidden_code').value  = code;
    document.getElementById('hidden_phone').value = phone;
    document.getElementById('continueForm').submit();
}
 
function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        btn.textContent = 'Copied!';
        setTimeout(() => btn.textContent = 'Copy', 2000);
    });
}
 
// Auto show verify form if there are errors
@if($errors->any())
    showVerifyForm();
@endif
</script>
 
</body>
</html>
 