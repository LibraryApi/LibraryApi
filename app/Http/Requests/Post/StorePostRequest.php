<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules()
    {
        return [
            'title' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:5|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            "required" => "поле :attribute обязательно к заполнению",
            "unique" => ":attribute должно быть уникальным",
            "min" => "минимальное количество символов - 5",
            "max" => "вы превысили максимальное количество символов",
            "string" => "Ожидается текстовое значение"
        ];
    }
}
