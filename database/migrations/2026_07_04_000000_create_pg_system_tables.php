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
        // 1. PG Buildings Table
        Schema::create('pg_buildings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });

        // 2. Rooms Table
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pg_building_id');
            $table->string('number');
            $table->string('type'); // Single Sharing, Double Sharing, Triple Sharing
            $table->integer('rent');
            $table->integer('capacity');
            $table->integer('occupied')->default(0);
            $table->timestamps();
        });

        // 3. Payments Table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('student_email');
            $table->unsignedBigInteger('pg_building_id');
            $table->string('room_number');
            $table->string('month'); // e.g. July 2026
            $table->integer('amount');
            $table->string('status')->default('Due'); // Paid, Due
            $table->string('payment_date')->nullable();
            $table->string('tx_id')->nullable();
            $table->string('method')->nullable();
            $table->timestamps();
        });

        // 4. Payment Config Table
        Schema::create('payment_configs', function (Blueprint $table) {
            $table->id();
            $table->string('bank_account')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('ifsc')->nullable();
            $table->string('upi_id')->nullable();
            $table->string('phonepe')->nullable();
            $table->string('gpay')->nullable();
            $table->string('paytm')->nullable();
            $table->string('other')->nullable();
            $table->timestamps();
        });

        // 5. Complaints Table
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('student_email');
            $table->string('student_name');
            $table->string('room_number');
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('Pending'); // Pending, Resolved
            $table->string('assigned_to')->nullable();
            $table->string('created_date')->nullable();
            $table->string('resolved_date')->nullable();
            $table->text('reply')->nullable();
            $table->timestamps();
        });

        // 6. Food Preferences Table
        Schema::create('food_preferences', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('name');
            $table->string('room');
            $table->string('date'); // YYYY-MM-DD
            // If true, it means OPTED OUT / NO FOOD
            $table->boolean('morning')->default(false);
            $table->boolean('afternoon')->default(false);
            $table->boolean('evening')->default(false);
            $table->timestamps();
        });

        // 7. Food Menus Table
        Schema::create('food_menus', function (Blueprint $table) {
            $table->id();
            $table->string('day'); // monday, tuesday, etc.
            $table->string('breakfast')->nullable();
            $table->string('lunch')->nullable();
            $table->string('dinner')->nullable();
            $table->timestamps();
        });

        // 8. Notices Table
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('date');
            $table->string('title');
            $table->text('content');
            $table->string('target')->default('all'); // all, student, staff
            $table->timestamps();
        });

        // 9. Landing Contents Table
        Schema::create('landing_contents', function (Blueprint $table) {
            $table->id();
            // Store as JSON or simple key-values for easier configuration
            $table->text('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->text('banner_title')->nullable();
            $table->text('banner_subtitle')->nullable();
            $table->text('banner_image')->nullable();
            $table->text('about_text')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('contact_address')->nullable();
            $table->longText('facilities_json')->nullable();
            $table->longText('pricing_plans_json')->nullable();
            $table->longText('testimonials_json')->nullable();
            $table->longText('locations_json')->nullable();
            $table->timestamps();
        });

        // 10. Inventory Table
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('item');
            $table->integer('count');
            $table->string('status')->default('In Stock');
            $table->timestamps();
        });

        // 11. System Notifications Table
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('date');
            $table->text('text');
            $table->string('type'); // admin, student, staff
            $table->boolean('read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pg_buildings');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_configs');
        Schema::dropIfExists('complaints');
        Schema::dropIfExists('food_preferences');
        Schema::dropIfExists('food_menus');
        Schema::dropIfExists('notices');
        Schema::dropIfExists('landing_contents');
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('system_notifications');
    }
};
