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
        $subscription = Subscription::first();
        $unitCount    = Unit::count();
        return view('subscription.index', compact('subscription', 'unitCount'));
    }

    // Admin — create subscription
    public function store(Request $request)
    {
        $request->validate([
            'client_name'  => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'nullable|string|max:20',
            'plan'         => 'required|in:starter,growth,pro,enterprise',
            'status'       => 'required|in:trial,active,expired,suspended',
            'trial_ends_at'=> 'nullable|date',
            'expires_at'   => 'nullable|date',
            'monthly_fee'  => 'required|numeric|min:0',
            'notes'        => 'nullable|string|max:500',
            'is_exempt'    => 'nullable|boolean',
        ]);

        $maxUnits = match($request->plan) {
            'starter'    => 20,
            'growth'     => 50,
            'pro'        => 100,
            'enterprise' => 99999,
            default      => 20,
        };

        Subscription::updateOrCreate(
            ['id' => 1],
            array_merge($request->all(), [
                'max_units'  => $maxUnits,
                'is_exempt'  => $request->boolean('is_exempt'),
            ])
        );

        return redirect()->route('subscription.index')
            ->with('success', 'Subscription updated successfully.');
    }

    // Activate subscription — extend by 30 days
    public function activate(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $subscription = Subscription::first();

        if (!$subscription) {
            return back()->with('error', 'No subscription found.');
        }

        $from = $subscription->expires_at && $subscription->expires_at->isFuture()
    ? $subscription->expires_at
    : now();

    $subscription->update([
        'status'     => 'active',
        'expires_at' => $from->addDays((int) $request->days),
    ]);

        return redirect()->route('subscription.index')
            ->with('success', "Subscription activated for {$request->days} days.");
    }

    // Suspend subscription
    public function suspend()
    {
        $subscription = Subscription::first();

        if ($subscription) {
            $subscription->update(['status' => 'suspended']);
        }

        return redirect()->route('subscription.index')
            ->with('success', 'Subscription suspended.');
    }
}