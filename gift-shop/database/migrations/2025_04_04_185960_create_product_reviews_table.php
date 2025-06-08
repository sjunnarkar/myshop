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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('rating')->comment('1-5 rating');
            $table->text('review')->nullable();
            $table->text('pros')->nullable();
            $table->text('cons')->nullable();
            $table->boolean('verified_purchase')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Ensure one review per user per product
            $table->unique(['product_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
}; 