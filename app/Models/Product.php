<?php

namespace App\Models;

use App\Models\Traits\CanBeScoped;
use App\Models\Traits\HasPrice;
use App\Scoping\Scoper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;
    use CanBeScoped;
    use HasPrice;

    protected $fillable = [
        'name_en',
        'name_ar',
        'slug',
        'price',
        'description',
        'on_order'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function inStock()
    {
        return $this->stockCount() > 0;
    }

    public function stockCount()
    {
        return $this->variations->sum(function ($variation){
            return $variation->stockCount();
        });
    }

    public function categories(){
        return $this->belongsToMany(Category::class);
    }

    public function variations(){
        return $this->hasMany(ProductVariation::class)->orderBy('order');
    }


    public function scopeWithScopes(Builder $builder,$scopes=[]){
        return (new Scoper(request()))->apply($builder,$scopes);
    }
}
