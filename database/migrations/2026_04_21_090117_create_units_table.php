<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('unit_number');
            $table->enum('type', ['bedsitter', 'single_room', 'one_bedroom', 'two_bedroom', 'three_bedroom', 'commercial'])->default('one_bedroom');
            $table->decimal('rent_amount', 10, 2)->default(0);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->enum('status', ['vacant', 'occupied', 'under_maintenance'])->default('vacant');
            $table->integer('floor_number')->default(0);
            $table->text('notes')->nullable();
            $table->boolean('has_water_meter')->default(false);
            $table->string('water_meter_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};