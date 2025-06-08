<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->boolean('is_shipping')->default(true)->after('is_default');
            $table->boolean('is_billing')->default(true)->after('is_shipping');
        });

        // Update existing addresses to be both shipping and billing
        DB::table('user_addresses')->update([
            'is_shipping' => true,
            'is_billing' => true
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropColumn(['is_shipping', 'is_billing']);
        });
    }
};
