<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('lease_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->decimal('rent_amount', 10, 2)->default(0);
            $table->decimal('water_amount', 10, 2)->default(0);
            $table->decimal('garbage_amount', 10, 2)->default(0);
            $table->decimal('other_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->date('due_date');
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('status', ['draft', 'sent', 'partial', 'paid', 'overdue'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['mpesa', 'cash', 'bank_transfer', 'cheque'])->default('mpesa');
            $table->string('reference_number')->nullable();
            $table->string('mpesa_transaction_id')->nullable();
            $table->date('payment_date');
            $table->enum('status', ['pending', 'confirmed', 'bounced'])->default('confirmed');
            $table->text('notes')->nullable();
            $table->boolean('whatsapp_sent')->default(false);
            $table->timestamp('whatsapp_sent_at')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};