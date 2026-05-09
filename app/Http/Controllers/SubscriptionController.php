<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Unit;
use App\Models\Setting;

class SubscriptionController extends Controller
{
    // Show expired page
    public function expired()
    {
        $subscription = Subscription::first();
        $companyName  = Setting::get('company_name', 'MakaziLink v2');
        $phone        = Setting::get('company_phone', '');
        return view('subscription.expired', compact('subscription', 'companyName', 'phone'));
    }

    // Admin — view subscription status
    public function index()
    {
        $subscription  = Subscription::first();
        $unitCount     = Unit::count();
        $tenantCount   = \App\Models\Tenant::count();
        $currency      = Setting::get('currency', 'KES');
        $calculatedFee = $tenantCount * 100;

        // Superadmin — if no subscription yet allow access to set it up
        // Otherwise redirect to dashboard
        if (auth()->user()->role === 'superadmin' && $subscription) {
            return redirect()->route('dashboard');
        }

        return view('subscription.index', compact(
            'subscription', 'unitCount', 'tenantCount', 'currency', 'calculatedFee'
        ));
    }

    // Create or update subscription
    public function store(Request $request)
    {
        $request->validate([
            'client_name'   => 'required|string|max:255',
            'client_email'  => 'required|email|max:255',
            'client_phone'  => 'nullable|string|max:20',
            'plan'          => 'required|in:starter,growth,pro,enterprise,tenant',
            'status'        => 'required|in:trial,active,expired,suspended',
            'trial_ends_at' => 'nullable|date',
            'expires_at'    => 'nullable|date',
            'monthly_fee'   => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string|max:500',
            'is_exempt'     => 'nullable',
        ]);

        $maxUnits = match($request->plan) {
            'starter'    => 20,
            'growth'     => 50,
            'pro'        => 100,
            'enterprise' => 99999,
            'tenant'     => 99999,
            default      => 20,
        };

        Subscription::updateOrCreate(
            ['id' => 1],
            [
                'client_name'   => $request->client_name,
                'client_email'  => $request->client_email,
                'client_phone'  => $request->client_phone,
                'plan'          => $request->plan,
                'status'        => $request->status,
                'trial_ends_at' => $request->trial_ends_at,
                'expires_at'    => $request->expires_at,
                'monthly_fee'   => $request->monthly_fee ?? 0,
                'notes'         => $request->notes,
                'is_exempt'     => $request->has('is_exempt') ? 1 : 0,
                'max_units'     => $maxUnits,
            ]
        );

        return redirect()->route('dashboard')
            ->with('success', 'Subscription saved successfully.');
    }

    // Activate or extend subscription
    public function activate(Request $request)
    {
        // Handle exempt only toggle
        if ($request->has('exempt_only')) {
            $subscription = Subscription::first();
            if ($subscription) {
                $subscription->update([
                    'is_exempt' => $request->boolean('is_exempt'),
                ]);
            }
            return redirect()->route('dashboard')
                ->with('success', 'Exempt status updated.');
        }

        $request->validate([
            'days' => 'required|integer|min:1',
        ]);

        $subscription = Subscription::first();

        if (!$subscription) {
            return redirect()->route('dashboard')
                ->with('error', 'No subscription found. Please set one up first.');
        }

        $from = $subscription->expires_at && $subscription->expires_at->isFuture()
            ? $subscription->expires_at
            : now();

        $activatedFrom = $from->copy();
        $activatedTo   = $from->copy()->addDays((int) $request->days);

        $subscription->update([
            'status'     => 'active',
            'expires_at' => $activatedTo,
        ]);

        // Record renewal history
        \App\Models\RenewalHistory::create([
            'days_added'     => (int) $request->days,
            'activated_from' => $activatedFrom,
            'activated_to'   => $activatedTo,
            'activated_by'   => auth()->user()->name,
            'method'         => 'manual',
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Subscription activated for ' . $request->days . ' days.');
    }

    // Suspend subscription
    public function suspend()
    {
        $subscription = Subscription::first();

        if ($subscription) {
            $subscription->update(['status' => 'suspended']);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Subscription suspended.');
    }

    // Save private notes
    public function saveNotes(Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        Setting::set('superadmin_notes', $request->notes ?? '');

        return redirect()->route('dashboard')
            ->with('success', 'Notes saved successfully.');
    }

    // Change superadmin password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->new_password),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Password changed successfully.');
    }

    // Save superadmin settings
    public function saveSettings(Request $request)
    {
        $request->validate([
            'my_paybill'      => 'nullable|string|max:20',
            'my_paybill_type' => 'required|in:paybill,till,phone',
            'system_version'  => 'nullable|string|max:20',
        ]);

        Setting::set('my_paybill',      $request->my_paybill ?? '');
        Setting::set('my_paybill_type', $request->my_paybill_type);
        Setting::set('system_version',  $request->system_version ?? '1.0.0');

        return redirect()->route('dashboard')
            ->with('success', 'Settings saved successfully.');
    }
}