<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:50',
            'content' => 'required|string',
            'number' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Страница не может быть пустой.',
            'title.max' => 'Максимальное количество символов для названия страницы — 50.',
            'number.integer' => 'Номер страницы должен быть целым числом.',
            'number.min' => 'Номер страницы должен быть больше нуля.',
        ];
    }
}
