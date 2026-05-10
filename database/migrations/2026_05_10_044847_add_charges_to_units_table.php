<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->decimal('water_charge', 10, 2)->default(0)->after('water_meter_number');
            $table->decimal('garbage_charge', 10, 2)->default(0)->after('water_charge');
            $table->decimal('service_charge', 10, 2)->default(0)->after('garbage_charge');
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['water_charge', 'garbage_charge', 'service_charge']);
        });
    }
};