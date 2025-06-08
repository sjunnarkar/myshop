<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop duplicate tables if they exist
        if (Schema::hasTable('newsletter_subscribers_old')) {
            Schema::drop('newsletter_subscribers_old');
        }
        if (Schema::hasTable('wishlists_old')) {
            Schema::drop('wishlists_old');
        }

        // Rename the duplicate tables to _old
        Schema::rename('newsletter_subscribers', 'newsletter_subscribers_old');
        
        // Create the final newsletter_subscribers table
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('status')->default('subscribed');
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('last_opened_at')->nullable();
            $table->timestamp('last_clicked_at')->nullable();
            $table->json('preferences')->nullable();
            $table->string('token')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('newsletter_subscribers');
        Schema::rename('newsletter_subscribers_old', 'newsletter_subscribers');
    }
}; 