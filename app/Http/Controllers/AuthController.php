<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
    $credentials = $request->validate([
    'email'    => ['required', 'email', 'max:255'],
    'password' => ['required', 'string', 'max:255'],
    ]);

    // Rate limiting — max 5 attempts per minute per IP
    $key = 'login_attempts_' . $request->ip();
    $attempts = cache()->get($key, 0);

    if ($attempts >= 5) {
        $seconds = cache()->getTimeToLive($key) ?? 60;
        return back()->withErrors([
            'email' => 'Too many login attempts. Please wait ' . ceil($seconds / 60) . ' minute(s) before trying again.',
        ])->onlyInput('email');
    }

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        // Clear failed attempts on success
        cache()->forget($key);
        $request->session()->regenerate();

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account has been deactivated.',
            ]);
        }

        return $this->redirectByRole($user->role);
    }

    // Increment failed attempts
    cache()->put($key, $attempts + 1, now()->addMinutes(1));

    return back()->withErrors([
        'email' => 'These credentials do not match our records.',
    ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin'      => redirect()->route('dashboard'),
            'agent'      => redirect()->route('dashboard'),
            'accountant' => redirect()->route('dashboard'),
            'caretaker'  => redirect()->route('dashboard'),
            'tenant'     => redirect()->route('tenant.portal'),
            default      => redirect()->route('dashboard'),
        };
    }
}