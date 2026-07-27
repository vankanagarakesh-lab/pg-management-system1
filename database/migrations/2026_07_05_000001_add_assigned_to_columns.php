<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add assigned_to to rooms table
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'assigned_to')) {
                $table->string('assigned_to')->nullable()->after('occupied'); // Housekeeping staff name
            }
        });

        // Add assigned_to to common_area_tasks table
        Schema::table('common_area_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('common_area_tasks', 'assigned_to')) {
                $table->string('assigned_to')->nullable()->after('status'); // Housekeeping staff name
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('common_area_tasks', function (Blueprint $table) {
            $table->dropColumn('assigned_to');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('assigned_to');
        });
    }
};
