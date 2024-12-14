<?php

namespace App\Models;

use App\Cart\Money;
use App\Enums\Orders\AOrderStatus;
use App\Models\Traits\HasPrice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    use HasPrice;

    protected $fillable = [
        'status_id',
        'user_id',
        'subtotal',
        'address_id'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function($order){
            $order->status_id = AOrderStatus::PENDING;
        });
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
    

    public function products()
    {
        return $this->belongsToMany(ProductVariation::class, 'product_variation_order')
            ->withPivot(['quantity'])
            ->withTimestamps();
    }


    public function getSubtotalAttribute($subtotal)
    {
        return new Money($subtotal);
    }

    public function total()
    {
        return $this->subtotal;
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
