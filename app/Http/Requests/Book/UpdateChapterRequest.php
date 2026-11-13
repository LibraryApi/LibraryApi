<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChapterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        $chapterId = $this->route('chapter'); // Получаем идентификатор главы из маршрута
        $bookId = $this->route('book');

        return [
            'title' => [
                'string',
                'min:5',
                'max:50',
                Rule::unique('chapters', 'title')
                    ->ignore($chapterId)
                    ->where(fn ($query) => $query->where('book_id', $bookId)),
            ],
            'content' => 'string|required_without_all:title,number,characters,images',
            'number' => 'integer',
            'characters' => 'nullable|string',
            'images' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'string' => 'Поле :attribute должно быть строкой',
            'integer' => 'Поле :attribute должно быть целым числом',
            'unique' => 'Глава с таким названием уже есть в этой книге.',

            'title.max' => 'Максимальное количество символов для :attribute - 50',
            'title.min' => 'Минимальное количество символов для :attribute - 5',

            'required_without_all' => 'Обязательно нужно внести изменения хотя бы в одно поле из либо content становится обязательным'
        ];
    }
}
