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
            'content' => 'string|min:5|max:300'
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'поле текст комментария обязательно к заполнению',
            'content.min' => 'Минимальное колличество символов равно 5',
            'content.max' => 'Максимальоне колличество символов равно 300'
        ];
    }
}
