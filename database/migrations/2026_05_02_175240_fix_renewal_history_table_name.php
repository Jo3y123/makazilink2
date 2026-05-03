<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('renewal_history', 'renewal_histories');
    }

    public function down(): void
    {
        Schema::rename('renewal_histories', 'renewal_history');
    }
};