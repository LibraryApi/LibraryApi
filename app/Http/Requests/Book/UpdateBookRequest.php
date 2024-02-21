<?php

namespace App\Http\Requests\book;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
    public function rules(): array
{
    return [
        "title" => "required|max:50|min:5|unique:books,title," . $this->route('book'), // Учитываем текущую книгу при уникальности
        "author" => "required|max:50|min:5",
        "description" => "required|min:10|max:10000",
        "cover_image" => "nullable", // Поле может быть nullable
        "author_bio" => "nullable", // Поле может быть nullable
        "language" => "nullable", // Поле может быть nullable
        "rating" => "nullable|numeric", // Поле может быть nullable и должно быть числом
        "number_of_pages" => "nullable|integer", // Поле может быть nullable и должно быть целым числом
        "is_published" => "boolean", // Должно быть булевым значением
    ];
}

public function messages(): array
{
    return [
        "required" => "это поле :attribute обязательно к заполнению",
        "unique" => ":attribute должно быть уникальным",
        "numeric" => ":attribute должно быть числом",
        "integer" => ":attribute должно быть целым числом",

        "author.max" => "Максимальное количество символов должно быть 50",
        "author.min" => "Минимальное количество символов должно быть 5",

        "title.max" => "Максимальное количество символов должно быть 50",
        "title.min" => "Минимальное количество символов должно быть 5",

        "description.max" => "Максимальное количество символов должно быть 10000",
        "description.min" => "Минимальное количество символов должно быть 10",
    ];
}
}
