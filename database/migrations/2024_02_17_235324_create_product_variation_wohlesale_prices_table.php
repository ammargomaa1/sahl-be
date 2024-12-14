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
        Schema::create('product_variation_wholesale_prices', function (Blueprint $table) {
            $table->id();
            $table->integer('min_quantity');
            $table->integer('price');
            $table->integer('product_variation_id')->unsigned()->index();
            $table->foreign('product_variation_id')->references('id')->on('product_variations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variation_wholesale_prices');
    }
};
