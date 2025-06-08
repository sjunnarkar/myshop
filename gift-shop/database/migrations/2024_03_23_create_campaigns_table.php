<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // email, social, display, search, etc.
            $table->text('description')->nullable();
            $table->decimal('budget', 10, 2)->default(0);
            $table->decimal('cost', 10, 2)->default(0);
            $table->integer('reach')->default(0);
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('conversions')->default(0);
            $table->decimal('revenue', 10, 2)->default(0);
            $table->string('status')->default('draft'); // draft, scheduled, active, completed, cancelled
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->json('targeting_criteria')->nullable();
            $table->json('platforms')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
}; 