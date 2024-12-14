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
        
        \Illuminate\Support\Facades\DB::statement("DROP VIEW IF EXISTS product_variation_stock_view");
        \Illuminate\Support\Facades\DB::statement("

        CREATE VIEW product_variation_stock_view AS
        SELECT
            product_variations.product_id AS product_id,
            product_variations.id,
            COALESCE (SUM(stocks.quantity),0) - COALESCE (SUM(product_variation_order.quantity),0) AS stock ,
            CASE WHEN COALESCE (SUM(stocks.quantity),0) - COALESCE (SUM(product_variation_order.quantity),0) > 0
                THEN true
                ELSE false
            END in_stock
        FROM product_variations
        LEFT JOIN (
            SELECT
                stocks.product_variation_id as id,
                SUM(stocks.quantity) as quantity
            FROM stocks
            GROUP BY stocks.product_variation_id
        ) AS stocks USING (id)
        LEFT JOIN (
            SELECT
                product_variation_order.product_variation_id as id,
                SUM(product_variation_order.quantity) as quantity
                FROM product_variation_order
                GROUP BY product_variation_order.product_variation_id
        ) AS product_variation_order USING (id)
        GROUP BY product_variations.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variation_stock_view');
    }
};
