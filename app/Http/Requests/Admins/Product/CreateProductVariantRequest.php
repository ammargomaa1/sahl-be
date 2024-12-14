<?php

namespace App\Http\Requests\Admins\Product;

use App\Rules\ValidateBase64Image;
use Illuminate\Foundation\Http\FormRequest;

class CreateProductVariantRequest extends FormRequest
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
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'price' => 'required|integer',
            'stock_count' => 'required|integer',
            'images' => 'required|array',
            'images.*' => ['required', new ValidateBase64Image()],
        ];
    }
}
