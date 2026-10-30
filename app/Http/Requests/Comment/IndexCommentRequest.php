<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class IndexCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'commentable_type' => $this->input('commentable_type'),
            'commentable_id' => $this->input('commentable_id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'commentable_type' => ['nullable', 'string', 'in:post,book,chapter,posts,books,chapters,App\\Models\\Post,App\\Models\\Book,App\\Models\\Chapter'],
            'commentable_id' => ['nullable', 'integer', 'min:1'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function filters(): array
    {
        return $this->validated();
    }

    public function perPage(): int
    {
        return (int) ($this->validated('per_page') ?? 6);
    }
}
