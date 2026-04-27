<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Subscription;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for guests
        if (!auth()->check()) {
            return $next($request);
        }

        // Always allow access to subscription and logout routes
        if ($request->routeIs('subscription.*') ||
            $request->routeIs('logout') ||
            $request->is('logout')) {
            return $next($request);
        }

        // Only enforce for admin role
        if (auth()->user()->role === 'admin') {
            $subscription = Subscription::first();

            // No subscription set up yet — allow access
            if (!$subscription) {
                return $next($request);
            }

            // Check if subscription is active
            if (!$subscription->isActive()) {
                return redirect()->route('subscription.expired');
            }
        }

        return $next($request);
    }
}