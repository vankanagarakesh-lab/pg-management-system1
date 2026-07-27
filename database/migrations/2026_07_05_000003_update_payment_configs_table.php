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
        Schema::table('payment_configs', function (Blueprint $table) {
            // Drop removed fields
            if (Schema::hasColumn('payment_configs', 'bank_account')) {
                $table->dropColumn('bank_account');
            }
            if (Schema::hasColumn('payment_configs', 'ifsc')) {
                $table->dropColumn('ifsc');
            }
            if (Schema::hasColumn('payment_configs', 'upi_id')) {
                $table->dropColumn('upi_id');
            }
            
            // Add qr_code field
            if (!Schema::hasColumn('payment_configs', 'qr_code')) {
                $table->string('qr_code')->nullable()->after('account_holder');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_configs', function (Blueprint $table) {
            $table->string('bank_account')->nullable();
            $table->string('ifsc')->nullable();
            $table->string('upi_id')->nullable();
            $table->dropColumn('qr_code');
        });
    }
};
