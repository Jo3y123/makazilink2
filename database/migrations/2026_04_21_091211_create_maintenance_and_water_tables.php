<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->enum('category', ['plumbing', 'electrical', 'structural', 'cleaning', 'pest_control', 'other'])->default('other');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->decimal('cost', 10, 2)->nullable();
            $table->date('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_request_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('caption')->nullable();
            $table->enum('photo_type', ['before', 'during', 'after'])->default('before');
            $table->timestamps();
        });

        Schema::create('water_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->decimal('previous_reading', 10, 3)->default(0);
            $table->decimal('current_reading', 10, 3)->default(0);
            $table->decimal('units_consumed', 10, 3)->default(0);
            $table->decimal('rate_per_unit', 10, 2)->default(0);
            $table->decimal('amount_charged', 10, 2)->default(0);
            $table->date('reading_date');
            $table->string('billing_period');
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('lease_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained()->onDelete('cascade');
            $table->enum('alert_type', ['30_days', '14_days', '7_days', '1_day', 'expired'])->default('30_days');
            $table->boolean('sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lease_alerts');
        Schema::dropIfExists('water_readings');
        Schema::dropIfExists('maintenance_photos');
        Schema::dropIfExists('maintenance_requests');
    }
};