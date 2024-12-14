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
        Schema::table('product_variation_wholesale_prices', function (Blueprint $table) {
            $table->unique(['min_quantity','product_variation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variation_wholesale_prices', function (Blueprint $table) {
            $table->dropUnique('min_quantity_product_variation_id');
        });
    }
};
