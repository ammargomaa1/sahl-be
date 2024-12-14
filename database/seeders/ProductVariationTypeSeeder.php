<?php

namespace Database\Seeders;

use App\Models\ProductVariationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductVariationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductVariationType::create([
            'id' => 1,
            'name' => 'color'
        ]);
    }
}
