<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class CheckSubscriptionExpiry extends Command
{
    protected $signature   = 'subscription:check-expiry';
    protected $description = 'Check subscription expiry and send reminders';

    public function handle()
    {
        $subscription = Subscription::first();

        if (!$subscription) {
            $this->info('No subscription found.');
            return;
        }

        if ($subscription->is_exempt) {
            $this->info('Subscription is exempt. Skipping.');
            return;
        }

        $daysRemaining = $subscription->daysRemaining();

        if ($daysRemaining <= 7 && $daysRemaining > 0) {
            // Store reminder in cache so dashboard can show it
            cache()->put('subscription_expiry_warning', [
                'days'       => $daysRemaining,
                'expires_at' => $subscription->expires_at?->format('d M Y'),
                'plan'       => $subscription->planLabel(),
            ], now()->addHours(24));

            Log::warning('Subscription expiring soon', [
                'days_remaining' => $daysRemaining,
                'expires_at'     => $subscription->expires_at,
                'plan'           => $subscription->plan,
            ]);

            $this->info("Warning: Subscription expires in {$daysRemaining} days.");

        } elseif ($daysRemaining === 0) {
            cache()->put('subscription_expiry_warning', [
                'days'       => 0,
                'expires_at' => $subscription->expires_at?->format('d M Y'),
                'plan'       => $subscription->planLabel(),
            ], now()->addHours(24));

            $this->info('Warning: Subscription expires today!');

        } else {
            // Clear warning if subscription is fine
            cache()->forget('subscription_expiry_warning');
            $this->info("Subscription is active. {$daysRemaining} days remaining.");
        }
    }
}