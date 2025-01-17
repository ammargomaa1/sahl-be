<?php

namespace App\Http\Requests\Admins\Product;

use App\Rules\ValidateBase64Image;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreProductRequest extends FormRequest
{
    public $slug;
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
        $this->merge([
            'slug' => Str::slug(request('name_ar'))
        ]);
        
        
        return [
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'slug' => 'required|unique:products,slug',
            'price' => 'required|integer|min:100',
            'description' => 'required|string',
            'on_order' => 'required|boolean',
            'categories' => 'array',
            'categories.*' =>'int|exists:categories,id',
            'variations' => 'required',
            'variations.*.name_ar' => 'required|string',
            'variations.*.name_en' => 'required|string',
            'variations.*.price' => 'required|integer|min:100',
            'variations.*.stock_count' => 'required|integer|min:1',
            'variations.*.images' => 'required|array',
            'variations.*.images.*' => ['required', new ValidateBase64Image()],
        ];
    }
}
