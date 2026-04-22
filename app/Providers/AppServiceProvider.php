<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blade::if('hasrole', function (string|array $roles) {
            if (!auth()->check()) {
                return false;
            }
            if (is_string($roles)) {
                return auth()->user()->role === $roles;
            }
            return in_array(auth()->user()->role, $roles);
        });
    }
}