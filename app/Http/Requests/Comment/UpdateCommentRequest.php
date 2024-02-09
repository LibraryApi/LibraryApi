<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'content' => 'required|string|min:5', // Пример: минимальная длина текста - 5 символов
        ];
    }

    public function messages()
    {
        return [
            'required' => 'поле :attribute обязательно к заполнению',
            'string' => 'Ожидается текстовое значение',
            'min' => 'минимальное количество символов - :min',
        ];
    }
}
