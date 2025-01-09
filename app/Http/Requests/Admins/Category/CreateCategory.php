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

    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->input('name_ar')),
        ]);

        if ($this->input('parent_id') == 'null') {
            $this->request->remove('parent_id');
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        
        return [
            'name_ar' => 'required',
            'name_en' => 'required',
            'parent_id' => ['nullable',Rule::exists('categories', 'id')->where(function ($builder) {
                $builder->whereNull('parent_id');
            })],
            'image' => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'slug' => 'unique:categories,slug',
            'is_main_page_menu' => 'required|boolean'
        ];
    }
}
