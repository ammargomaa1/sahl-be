<?php

namespace App\Http\Requests\Admins\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateProductVariantWholeSalePrice extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'prices' => 'required|array',
            'prices.*.min_quantity' => ['required', 'integer', Rule::unique('product_variation_wholesale_prices')->where(function ($query) {
                return $query->where('product_variation_id', request()->route('product_variant')->id);
            }),],
            'prices.*.price' => 'required|integer'
        ];
    }
}
