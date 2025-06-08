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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->enum('type', ['percentage', 'fixed', 'buy_x_get_y']);
            $table->decimal('value', 10, 2);
            $table->integer('buy_x')->nullable(); // For buy X get Y type
            $table->integer('get_y')->nullable(); // For buy X get Y type
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('applicable_products')->nullable();
            $table->json('applicable_categories')->nullable();
            $table->decimal('minimum_spend', 10, 2)->nullable();
            $table->decimal('maximum_discount', 10, 2)->nullable();
            $table->integer('usage_limit_per_user')->nullable();
            $table->boolean('stackable')->default(false); // Can be combined with other discounts
            $table->integer('priority')->default(0); // Higher priority discounts are applied first
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
}; 