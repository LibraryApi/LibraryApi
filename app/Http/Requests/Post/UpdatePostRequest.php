<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules()
    {
        return [
            'title' => 'string|min:5|max:255',
            'content' => 'string|min:5|max:10000',
            'book_id' => 'nullable|exists:books,id',
        ];
    }

    public function messages(): array
    {
        return [
            "unique" => ":attribute должно быть уникальным",
            "min" => "минимальное количество символов - 5",
            "string" => "Ожидается текстовое значение",

            "title.max" => "Максимальное колличество символов 255",
            "content.max" => "Максимальное колличество символов 1000",
        ];
    }
}
