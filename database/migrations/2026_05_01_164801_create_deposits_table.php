<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('lease_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_expected', 10, 2)->default(0);
            $table->decimal('amount_received', 10, 2)->default(0);
            $table->date('date_received')->nullable();
            $table->enum('status', ['pending', 'partial', 'received'])->default('pending');
            $table->decimal('deduction_amount', 10, 2)->default(0);
            $table->text('deduction_reason')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->date('refund_date')->nullable();
            $table->enum('refund_method', ['cash', 'mpesa', 'bank_transfer'])->nullable();
            $table->string('refund_reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};