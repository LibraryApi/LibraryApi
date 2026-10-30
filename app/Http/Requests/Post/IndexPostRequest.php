<?php

namespace App\Http\Requests\Post;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class IndexPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $categoryIds = $this->input('category_ids', $this->input('categories'));

        $this->merge([
            'search' => trim((string) $this->input('search', '')),
            'author' => trim((string) $this->input('author', '')),
            'category' => trim((string) $this->input('category', '')),
            'category_ids' => $this->normalizeCategoryIds($categoryIds),
            'tags' => $this->normalizeTags($this->input('tags')),
        ]);
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'author_id' => ['nullable', 'integer', 'exists:users,id'],
            'category' => ['nullable', 'string', 'max:255'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'startDate' => ['nullable', 'date'],
            'endDate' => ['nullable', 'date'],
            'timezone' => ['nullable', 'string', 'max:64', 'timezone'],
            'minRating' => ['nullable', 'numeric', 'between:0,5'],
            'maxRating' => ['nullable', 'numeric', 'between:0,5'],
            'minLikes' => ['nullable', 'integer', 'min:0'],
            'maxLikes' => ['nullable', 'integer', 'min:0'],
            'tags' => ['nullable', 'array', 'max:20'],
            'tags.*' => ['string', 'max:50'],
            'sort' => ['nullable', 'in:date_desc,date_asc,likes_desc,likes_asc,rating_desc,rating_asc,title_asc,title_desc'],
            'sortDate' => ['nullable', 'in:asc,desc'],
            'sortName' => ['nullable', 'in:asc,desc'],
            'sortLikes' => ['nullable', 'in:asc,desc'],
            'sortRating' => ['nullable', 'in:asc,desc'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateRange($validator);
        });
    }

    public function filters(): array
    {
        return $this->validated();
    }

    public function perPage(): int
    {
        return (int) ($this->validated('per_page') ?? 12);
    }

    private function normalizeCategoryIds(mixed $raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }

        $values = is_array($raw) ? $raw : explode(',', (string) $raw);
        $ids = [];

        foreach ($values as $value) {
            $value = trim((string) $value);
            if ($value === '') {
                continue;
            }
            if (!ctype_digit($value)) {
                throw ValidationException::withMessages([
                    'category_ids' => 'Идентификаторы категорий должны быть целыми числами.',
                ]);
            }
            $ids[] = (int) $value;
        }

        $ids = array_values(array_unique($ids));

        if ($ids !== [] && Category::whereIn('id', $ids)->count() !== count($ids)) {
            throw ValidationException::withMessages([
                'category_ids' => 'Одна или несколько категорий не найдены.',
            ]);
        }

        return $ids;
    }

    private function normalizeTags(mixed $raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }

        $values = is_array($raw) ? $raw : explode(',', (string) $raw);
        $tags = [];

        foreach ($values as $value) {
            $value = trim((string) $value);
            if ($value !== '') {
                $tags[] = $value;
            }
        }

        return array_values(array_unique($tags));
    }

    private function validateRange($validator): void
    {
        if (!empty($this->input('startDate')) && !empty($this->input('endDate'))) {
            $start = Carbon::parse($this->input('startDate'))->startOfDay();
            $end = Carbon::parse($this->input('endDate'))->endOfDay();

            if ($start->greaterThan($end)) {
                $validator->errors()->add('endDate', 'Дата окончания не может быть раньше даты начала.');
            }
        }

        $minLikes = (int) ($this->input('minLikes') ?? 0);
        $maxLikes = (int) ($this->input('maxLikes') ?? 0);

        if ($maxLikes > 0 && $minLikes > $maxLikes) {
            $validator->errors()->add('maxLikes', 'Максимальное количество лайков не может быть меньше минимального.');
        }

        $minRating = (float) ($this->input('minRating') ?? 0);
        $maxRating = (float) ($this->input('maxRating') ?? 5);

        if ($maxRating < 5 && $minRating > $maxRating) {
            $validator->errors()->add('maxRating', 'Максимальный рейтинг не может быть меньше минимального.');
        }
    }
}
