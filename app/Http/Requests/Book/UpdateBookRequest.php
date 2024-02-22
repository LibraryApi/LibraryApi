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
            "title" => "max:50|min:5|unique:books,title," . $this->route('book'), // Учитываем текущую книгу при уникальности
            "author" => "max:50|min:5",
            "description" => "required_without_all:title,author,cover_image,author_bio,language,rating,number_of_pages,is_published,categories|min:10|max:10000",
            "cover_image" => "nullable", // Поле может быть nullable
            "author_bio" => "nullable", // Поле может быть nullable
            "language" => "nullable", // Поле может быть nullable
            "rating" => "nullable|numeric", // Поле может быть nullable и должно быть числом
            "number_of_pages" => "nullable|integer", // Поле может быть nullable и должно быть целым числом
            "is_published" => "boolean", // Должно быть булевым значением
            'categories.*' => 'nullable|integer|exists:categories,id',
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
            "categories.*.exists" => 'Вы передали несуществующую категорию.',
            'required_without_all' => 'Обязательно нужно внести изменения хотя бы в одно поле либо description становится обязательным'
        ];
    }

    public function attributes()
    {
        return [
            'categories' => 'категории',
        ];
    }
}
