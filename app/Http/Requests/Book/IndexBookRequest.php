<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class IndexBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $rawCategories = $this->input('category_ids');
        $categories = $rawCategories === null || $rawCategories === ''
            ? []
            : (is_array($rawCategories) ? $rawCategories : explode(',', (string) $rawCategories));

        $this->merge([
            'search' => trim((string) $this->input('search', '')),
            'author' => trim((string) $this->input('author', '')),
            'category' => trim((string) $this->input('category', '')),
            'category_ids' => array_values(array_filter(array_map('intval', $categories))),
        ]);
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'tags' => ['nullable'],
            'startDate' => ['nullable', 'date'],
            'endDate' => ['nullable', 'date'],
            'minRating' => ['nullable', 'numeric', 'between:0,10'],
            'sort' => ['nullable', 'in:date_desc,date_asc,title_asc,title_desc,rating_desc,rating_asc,likes_desc,likes_asc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function filters(): array
    {
        return $this->validated();
    }

    public function perPage(): int
    {
        return (int) ($this->validated('per_page') ?? 12);
    }
}
