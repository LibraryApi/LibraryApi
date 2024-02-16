<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules()
    {
        return [
            'content' => 'required|string|min:5|max:300',
            'commentable_id' => 'required|int|min:1',
            'commentable_type'=> 'required|in:book,post|string'
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'поле текст комментария обязательно к заполнению',
            'content.min' => 'Минимальное колличество символов равно 5',
            'content.max' => 'Максимальоне колличество символов равно 300',

            'commentable_id.required' => 'поле идентификатор комментируемого объекта обязательно к заполнению',
            'commentable_type.required' => 'поле тип комментируемого объекта обязательно к заполнению',
            'commentable_id.integer' => 'поле идентификатор комментируемого объекта должен быть числом',

            "commentable_id" => 'Данные поле не может быть пустым'
        ];
    }

}
