<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customization_template_product', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('customization_template_id');
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->foreign('customization_template_id')
                ->references('id')
                ->on('customization_templates')
                ->onDelete('cascade');

            $table->primary(['product_id', 'customization_template_id'], 'ctp_primary');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customization_template_product');
    }
}; 