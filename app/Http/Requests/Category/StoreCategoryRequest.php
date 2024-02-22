<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255|unique:categories',
 
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
