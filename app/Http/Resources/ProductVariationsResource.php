<?php

namespace App\Http\Resources;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ProductVariationsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        if ($this->resource instanceof Collection) {
            return ProductVariationsResource::collection($this->resource);
        }

        return $this->getDataArr();
    }

    protected function getDataArr()
    {
        $data = [
            'id' => $this->id,
            'name_en' => $this->product->name_en. ' ' . $this->name_en,
            'name_ar' => $this->product->name_ar. ' ' .$this->name_ar,
            'price' => $this->formattedPrice,
            'price_varies' => $this->priceVaries(),
            // 'stock_count' => (int) $this->stockCount(),
            'stock_count' => $this->pivot?->quantity,
            'in_stock' => $this->inStock(),
            'images' => $this->productVariationImages,
            'money' => new MoneyResource($this->price),
            // 'product' => new ProductsIndexResource($this->product),
        ];

        if (request()->user() && (request()->user()->is_business || request()->user() instanceof Admin)) {
            $data['wholesale_prices'] = ProductVariationWholesalePrices::collection($this->wholesalePrices);
        }

        return $data;
    }
}
