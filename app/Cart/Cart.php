<?php

namespace App\Cart;

use App\Models\ShippingMethod;
use App\Models\User;

class Cart
{
    protected $user;

    protected $changed = false;

    protected $shipping;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function add($products)
    {
        $this->user->cart()->syncWithoutDetaching(
            $this->getStorePayload($products)
        );
    }

    public function update($productId, $quantity)
    {
        $this->user->cart()->updateExistingPivot($productId, [
            'quantity' => $quantity
        ]);
    }

    public function isEmpty()
    {
        return $this->user->cart->sum('pivot.quantity') <= 0;
    }

    public function delete($productId)
    {
        $this->user->cart()->detach($productId);
    }

    public function empty()
    {
        $this->user->cart()->detach();
    }

    public function subTotal()
    {
        $subtotal = $this->user->cart->sum(function ($product) {
            return $product->price->amount() * $product->pivot->quantity;
        }) ;

        return new Money($subtotal);
    }

    public function total()
    {
        if ($this->shipping) {
            return $this->subTotal()->add($this->shipping->price);
        }

        return $this->subTotal();
    }

    public function sync()
    {
        return $this->user->cart->each(function ($product) {
            $quantity = $product->minStock($product->pivot->quantity);

            if ($quantity != $product->pivot->quantity) {
                $this->changed = true;
            }

            

            $product->pivot->update([
                'quantity' => $quantity,
            ]);
        });
    }

    public function hasChanged()
    {
        return $this->changed;
    }

    public function products()
    {
        return $this->user->cart;
    }

    protected function getStorePayload($products)
    {
        return collect($products)->keyBy('id')->map(function ($product) {
            return [
                'quantity' => $product['quantity'] + $this->getCurrentQuantity($product['id'])
            ] ;
        })->toArray();
    }

    protected function getCurrentQuantity($productId)
    {
        if ($product = $this->user->cart->where('id', $productId)->first()) {
            return $product->pivot->quantity;
        }

        return 0 ;
    }
}
