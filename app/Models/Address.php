<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address_1',
        'city',
        'postal_code',
        'city_id',
        'default',
        'user_id'
    ];

    public static function boot(){
        parent::boot();

        static::creating(function($address){
            if($address->default){
                $address->user->addresses()->update([
                    'default' => false
                ]);
            }
        });
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function city(){
        return $this->hasOne(City::class,'id','city_id');
    }
}
