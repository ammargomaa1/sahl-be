<?php

namespace App\Http\Requests\Admins\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CreateCategory extends FormRequest
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
            'name_ar' => 'required',
            'name_en' => 'required',
            'parent_id' => [Rule::exists('categories', 'id')->where(function ($builder) {
                $builder->whereNull('parent_id');
            })],
            'image' => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'slug' => 'unique:categories,slug'
        ];
    }
}
