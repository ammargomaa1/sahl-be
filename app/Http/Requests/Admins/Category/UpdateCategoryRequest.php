<?php

namespace App\Http\Requests\Admins\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
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
            'name_ar' => 'string',
            'name_en' => 'string',
            'parent_id' => ['nullable', Rule::exists('categories', 'id')->where(function ($builder) {
                $builder->whereNull('parent_id');
            }), 'nullable'],
            'slug' => 'unique:categories,slug'
        ];
    }
}
