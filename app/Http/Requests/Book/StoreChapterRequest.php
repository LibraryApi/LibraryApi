<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class StoreChapterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'string|min:5|max:50|unique:chapters,title,' . $this->route('chapter'), // Указываем игнорирование уникальности для текущей главы
            'content' => 'string',
            'number' => 'integer',
            'duration' => 'nullable|string',
            'characters' => 'nullable|string',
            'images' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'unique' => ':Attribute должно быть уникальным',

            'title.max' => 'Максимальное количество символов для :attribute - 50',
            'title.min' => 'Минимальное количество символов для :attribute - 5',

            'content.string' => 'Поле :attribute должно быть строкой',

            'number.integer' => 'Поле :attribute должно быть целым числом',

            'duration.string' => 'Поле :attribute должно быть строкой',
            'characters.string' => 'Поле :attribute должно быть строкой',
            'images.string' => 'Поле :attribute должно быть строкой',
        ];
    }
}
