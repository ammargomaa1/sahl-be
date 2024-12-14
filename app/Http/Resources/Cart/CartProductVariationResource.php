<?php

namespace App\Http\Resources\Cart;

use App\Cart\Money;
use App\Http\Resources\ProductsIndexResource;
use App\Http\Resources\ProductVariationsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductVariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $variant = collect($request->user()->cart->where('product_id', $this->product->id))->first();

        return  [
            'product' => new ProductsIndexResource($this->product),
            'quantity' => $this->pivot->quantity,
            'total' => $this->getTotal()->formatted(),
            'variant' => new ProductVariationsResource((object)$variant)
        ];
    }

    protected function getTotal()
    {
        return new Money($this->pivot->quantity * $this->price->amount());
    }
}
