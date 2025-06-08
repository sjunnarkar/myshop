<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_customization_template', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('customization_template_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['product_id', 'customization_template_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_customization_template');
    }
}; 