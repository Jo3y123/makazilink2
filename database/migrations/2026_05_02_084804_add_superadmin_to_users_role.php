<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite does not support modifying check constraints
        // We recreate the column without the constraint
        DB::statement('PRAGMA foreign_keys=off;');

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('tenant')->change();
        });

        DB::statement('PRAGMA foreign_keys=on;');
    }

    public function down(): void
    {
        //
    }
};