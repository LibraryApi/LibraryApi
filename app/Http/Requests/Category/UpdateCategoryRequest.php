<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $categoryId = $this->route('category');


        return [
            'name' => "required|string|min:3|max:255|unique:categories,name,{$categoryId}",
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Поле :attribute обязательно для заполнения.',
            'string' => 'Поле :attribute должно быть строкой.',
            'max' => 'Максимальная длина для :attribute - :max символов.',
            'min' => 'Минимальная длина для :attribute - :max символов.',
            'unique' => ':Attribute уже существует.',
        ];
    }
}
