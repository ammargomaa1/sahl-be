<?php

namespace App\Http\Resources;

use App\Cart\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationWholesalePrices extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'min_quantity' => $this->min_quantity,
            'price' => (new Money($this->price))->formatted(),
            'money' => [
                'amount' => $this->price,
                'currency' => "SAR"
            ]
        ];
    }
}
