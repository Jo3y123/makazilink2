<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_email')->unique();
            $table->string('client_phone')->nullable();
            $table->enum('plan', ['starter', 'growth', 'pro', 'enterprise'])->default('starter');
            $table->enum('status', ['trial', 'active', 'expired', 'suspended'])->default('trial');
            $table->date('trial_ends_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->integer('max_units')->default(20);
            $table->decimal('monthly_fee', 10, 2)->default(2500);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};