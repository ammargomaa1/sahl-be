<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariationWholesalePrice extends Model
{
    use HasFactory;

    public $fillable = [
        'min_quantity',
        'price'
    ];
}
