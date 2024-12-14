<?php

namespace App\Models;

use App\Models\Traits\HasChildren;
use App\Models\Traits\IsOrderable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    use HasChildren;
    use IsOrderable;
    protected $fillable = [
        'name_ar',
        'name_en',
        'order',
        'slug',
        'parent_id'
    ];

    public function children(){
        return $this->hasMany(Category::class,'parent_id','id');
    }

    public function products(){
        return $this->belongsToMany(Product::class);
    }

    public function images(){
        return $this->hasMany(CategoryImage::class);
    }

    public function parent(){
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
