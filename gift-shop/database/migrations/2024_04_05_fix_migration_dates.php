<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update migration dates in the migrations table
        $migrations = [
            '2025_04_04_192031_add_is_admin_to_users_table' => '2024_04_04_192031_add_is_admin_to_users_table',
            '2025_04_04_191108_create_customization_templates_table' => '2024_04_04_191108_create_customization_templates_table',
            '2025_04_04_190813_create_order_items_table' => '2024_04_04_190813_create_order_items_table',
            '2025_04_04_185909_create_orders_table' => '2024_04_04_185909_create_orders_table',
            '2025_04_04_185838_create_products_table' => '2024_04_04_185838_create_products_table',
            '2025_04_04_185735_create_categories_table' => '2024_04_04_185735_create_categories_table',
        ];

        foreach ($migrations as $old => $new) {
            DB::table('migrations')
                ->where('migration', $old)
                ->update(['migration' => $new]);
        }
    }

    public function down()
    {
        // Revert migration dates
        $migrations = [
            '2024_04_04_192031_add_is_admin_to_users_table' => '2025_04_04_192031_add_is_admin_to_users_table',
            '2024_04_04_191108_create_customization_templates_table' => '2025_04_04_191108_create_customization_templates_table',
            '2024_04_04_190813_create_order_items_table' => '2025_04_04_190813_create_order_items_table',
            '2024_04_04_185909_create_orders_table' => '2025_04_04_185909_create_orders_table',
            '2024_04_04_185838_create_products_table' => '2025_04_04_185838_create_products_table',
            '2024_04_04_185735_create_categories_table' => '2025_04_04_185735_create_categories_table',
        ];

        foreach ($migrations as $old => $new) {
            DB::table('migrations')
                ->where('migration', $old)
                ->update(['migration' => $new]);
        }
    }
}; 