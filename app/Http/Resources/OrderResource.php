<?php

namespace App\Http\Resources;

use App\Enums\Orders\AOrderStatus;
use App\Http\Resources\Users\Auth\PrivateUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'status' => is_object($this->status_id) ? $this->status_id->name :  AOrderStatus::getOrderStatus($this->status_id),
            'created_at' => $this->created_at->toDateTimeString(),
            'subtotal' => $this->subtotal->formatted(),
            'total' => $this->total()->formatted(),
            'address' => new AddressResource($this->whenLoaded('address')),
            'user' => new PrivateUserResource($this->whenLoaded('user')),
            'products' => ProductVariationsResource::collection($this->whenLoaded('products'))
        ];

        if (!empty($this->redirect_url)) {
            $data['redirect_url'] = $this->redirect_url;
        }

        return $data;
    }
}
