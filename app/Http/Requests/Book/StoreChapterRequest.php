<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChapterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $bookId = $this->route('book');

        return [
            'title' => [
                'required',
                'string',
                'min:5',
                'max:50',
                Rule::unique('chapters', 'title')->where(
                    fn ($query) => $query->where('book_id', $bookId)
                ),
            ],
            'content' => 'nullable|string',
            'number' => 'integer',
            'characters' => 'nullable|string',
            'images' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Укажите название главы.',
            'title.unique' => 'Глава с таким названием уже есть в этой книге.',
            'title.max' => 'Максимальное количество символов для :attribute - 50',
            'title.min' => 'Минимальное количество символов для :attribute - 5',

            'content.string' => 'Поле :attribute должно быть строкой',

            'number.integer' => 'Поле :attribute должно быть целым числом',

            'characters.string' => 'Поле :attribute должно быть строкой',
            'images.string' => 'Поле :attribute должно быть строкой',
        ];
    }
}
