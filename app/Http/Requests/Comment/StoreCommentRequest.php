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
            'content' => 'required|string',
            'commentable_id' => 'required|int',
            'commentable_type'=> 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'поле текст комментария обязательно к заполнению',
            'commentable_id.required' => 'поле идентификатор комментируемого объекта обязательно к заполнению',
            'commentable_type.required' => 'поле тип комментируемого объекта обязательно к заполнению',
            'commentable_id.integer' => 'поле идентификатор комментируемого объекта должен быть числом',
        ];
    }

}
