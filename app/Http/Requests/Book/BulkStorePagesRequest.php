<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class BulkStorePagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pages' => 'required|array|min:1|max:100',
            'pages.*.title' => 'nullable|string|max:50',
            'pages.*.content' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'pages.required' => 'Добавьте хотя бы одну страницу.',
            'pages.max' => 'За один раз можно добавить не больше 100 страниц.',
            'pages.*.content.required' => 'У каждой страницы должен быть текст.',
            'pages.*.title.max' => 'Название страницы — не длиннее 50 символов.',
        ];
    }
}
