<?php

namespace App\Http\Resources\Users\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivateUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return[
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'is_business' => $this->is_business,
            'phone_number' => $this->phone_number,
            'email_verified' => $this->email_verified_at? true: false
        ];
    }
}
