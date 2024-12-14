<?php

namespace App\Http\Requests\Admins\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name_ar' => 'string',
            'name_en' => 'string',
            'slug' => 'alpha_dash|unique:products,slug',
            'price' => 'integer',
            'description' => 'string',
            'on_order' => 'boolean',
            'categories' => 'array',
            'categories.*' =>'int|exists:categories,id',
        ];
    }
}
