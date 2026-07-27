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
        Schema::table('landing_contents', function (Blueprint $table) {
            $table->string('logo_text')->nullable();
            $table->string('logo_image')->nullable();
            $table->string('pg_title')->nullable();
            $table->string('banner_tag')->nullable();
            $table->string('about_badge')->nullable();
            $table->string('about_title')->nullable();
            $table->string('facilities_title')->nullable();
            $table->string('facilities_subtitle')->nullable();
            $table->string('rooms_title')->nullable();
            $table->string('rooms_subtitle')->nullable();
            $table->string('locations_title')->nullable();
            $table->string('locations_subtitle')->nullable();
            $table->string('pricing_title')->nullable();
            $table->string('pricing_subtitle')->nullable();
            $table->string('contact_title')->nullable();
            $table->string('contact_subtitle')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_contents', function (Blueprint $table) {
            $table->dropColumn([
                'logo_text', 'logo_image', 'pg_title', 'banner_tag',
                'about_badge', 'about_title', 'facilities_title', 'facilities_subtitle',
                'rooms_title', 'rooms_subtitle', 'locations_title', 'locations_subtitle',
                'pricing_title', 'pricing_subtitle', 'contact_title', 'contact_subtitle'
            ]);
        });
    }
};
