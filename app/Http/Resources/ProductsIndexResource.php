<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsIndexResource extends JsonResource
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
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'slug' => $this->slug,
            'price' => $this->formattedPrice,
            'description' => $this->description,
            'stock_count' => $this->stockCount(),
            'in_stock' => $this->inStock(),
            'on_order' => $this->on_order,
            'images' => $this->variations->first()?->productVariationImages,
            'categories' => CategoryResource::collection($this->categories),
        ];
    }
}
