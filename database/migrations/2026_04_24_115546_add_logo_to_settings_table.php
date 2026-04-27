<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Logo is stored as a setting key/value like other settings
        // No schema change needed — we use the existing settings table
        // Just seed the default logo_path setting
        \App\Models\Setting::set('logo_path', '', 'general');
    }

    public function down(): void
    {
        \App\Models\Setting::where('key', 'logo_path')->delete();
    }
};