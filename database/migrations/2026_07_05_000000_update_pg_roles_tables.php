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
        // 1. Add staff_role to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'staff_role')) {
                $table->string('staff_role')->nullable()->after('role'); // Housekeeping, Food Management, Maintenance
            }
        });

        // 2. Add maintenance/details columns to complaints table
        Schema::table('complaints', function (Blueprint $table) {
            if (!Schema::hasColumn('complaints', 'priority')) {
                $table->string('priority')->default('Medium')->after('status'); // Low, Medium, High, Emergency
            }
            if (!Schema::hasColumn('complaints', 'category')) {
                $table->string('category')->default('Other')->after('priority'); // Electrical, Plumbing, Furniture, Wi-Fi, Water, Food, Other
            }
            if (!Schema::hasColumn('complaints', 'materials_used')) {
                $table->text('materials_used')->nullable()->after('category');
            }
            if (!Schema::hasColumn('complaints', 'repair_expense')) {
                $table->integer('repair_expense')->default(0)->after('materials_used');
            }
            if (!Schema::hasColumn('complaints', 'verification_status')) {
                $table->string('verification_status')->default('Pending')->after('repair_expense'); // Pending, Verified, Unresolved
            }
        });

        // 3. Create common_area_tasks table for Housekeeping
        Schema::create('common_area_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pg_building_id');
            $table->string('area_name'); // e.g. Corridor, Dining Hall, Lawn, Kitchen, Bathrooms
            $table->string('status')->default('Pending'); // Pending, Cleaned
            $table->string('last_cleaned_at')->nullable();
            $table->timestamps();
        });

        // 4. Create work_reports table for daily staff logs
        Schema::create('work_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('staff_role'); // Housekeeping, Food Management, Maintenance
            $table->text('report_text');
            $table->string('date'); // YYYY-MM-DD
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_reports');
        Schema::dropIfExists('common_area_tasks');

        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['priority', 'category', 'materials_used', 'repair_expense', 'verification_status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('staff_role');
        });
    }
};
