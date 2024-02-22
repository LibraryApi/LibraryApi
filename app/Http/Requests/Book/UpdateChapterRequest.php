<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

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

        return [
            'title' => "string|min:5|max:50|unique:chapters,title,{$chapterId}",
            'content' => 'string|required_without_all:title,number,duration,characters,images',
            'number' => 'integer',
            'duration' => 'nullable|string',
            'characters' => 'nullable|string',
            'images' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'string' => 'Поле :attribute должно быть строкой',
            'integer' => 'Поле :attribute должно быть целым числом',
            'unique' => ':Attribute должно быть уникальным',

            'title.max' => 'Максимальное количество символов для :attribute - 50',
            'title.min' => 'Минимальное количество символов для :attribute - 5',

            'required_without_all' => 'Обязательно нужно внести изменения хотя бы в одно поле из либо content становится обязательным'
        ];
    }
}
