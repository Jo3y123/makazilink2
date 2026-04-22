<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MakaziLink v2 — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f2d1e;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: #1a7a4a;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #fff;
        }

        .brand-text .name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f2d1e;
            line-height: 1.1;
        }

        .brand-text .tagline {
            font-size: .72rem;
            color: #6c757d;
        }

        h2 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 6px;
        }

        .subtitle {
            font-size: .82rem;
            color: #6c757d;
            margin-bottom: 24px;
        }

        .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }

        .form-control {
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: .875rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: border-color .2s;
        }

        .form-control:focus {
            border-color: #1a7a4a;
            box-shadow: 0 0 0 3px rgba(26,122,74,.1);
        }

        .input-group-text {
            background: #f9fafb;
            border: 1.5px solid #e5e7eb;
            border-right: none;
            color: #6c757d;
        }

        .input-group .form-control {
            border-left: none;
        }

        .btn-login {
            background: #1a7a4a;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 11px;
            font-size: .9rem;
            font-weight: 600;
            width: 100%;
            transition: background .2s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .btn-login:hover { background: #155c38; color: #fff; }

        .invalid-feedback { font-size: .78rem; }

        .footer-note {
            text-align: center;
            margin-top: 20px;
            font-size: .73rem;
            color: rgba(255,255,255,.3);
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="brand">
            <div class="brand-icon">
                <i class="bi bi-buildings"></i>
            </div>
            <div class="brand-text">
                <div class="name">MakaziLink v2</div>
                <div class="tagline">Rental Management System</div>
            </div>
        </div>

        <h2>Welcome back</h2>
        <p class="subtitle">Sign in to your account to continue</p>

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        autocomplete="email"
                        autofocus
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember" style="font-size:.8rem">
                        Remember me
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>
    </div>

    <div class="footer-note">
        &copy; {{ date('Y') }} MakaziLink &mdash; Built for Kenyan Landlords
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>