<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
            "title" => "required|max:50|min:5|unique:books,title",
            "author" => "required|max:50|min:5",
            "description" => "min:10|max:10000|required",
            "cover_image" => "nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
            "author_bio" => "nullable|string|max:255",
            "language" => "nullable|string|max:10",
            "rating" => "nullable|numeric|min:0|max:10",
            "number_of_pages" => "nullable|integer|min:1",
            "is_published" => "nullable|boolean",
        ];
    }
    public function messages(): array
    {
        return [
            "required" => "Это поле :attribute обязательно к заполнению",
        "unique" => ":attribute должно быть уникальным",

        "author.max" => "Максимальное количество символов для автора должно быть 50",
        "author.min" => "Минимальное количество символов для автора должно быть 5",

        "title.max" => "Максимальное количество символов для заголовка должно быть 50",
        "title.min" => "Минимальное количество символов для заголовка должно быть 5",

        "description.max" => "Максимальное количество символов для описания должно быть 10000",
        "description.min" => "Минимальное количество символов для описания должно быть 10",

        "cover_image.image" => "Файл должен быть изображением",
        "cover_image.mimes" => "Формат изображения должен быть jpeg, png, jpg или gif",
        "cover_image.max" => "Максимальный размер изображения - 2 МБ",

        "author_bio.max" => "Максимальное количество символов для биографии автора должно быть 255",

        "language.max" => "Максимальное количество символов для языка должно быть 10",

        "rating.numeric" => "Рейтинг должен быть числом",
        "rating.min" => "Рейтинг не может быть меньше 0",
        "rating.max" => "Рейтинг не может быть больше 10",

        "number_of_pages.integer" => "Количество страниц должно быть целым числом",
        "number_of_pages.min" => "Количество страниц не может быть меньше 1",

        "is_published.boolean" => "Поле is_published должно быть булевым значением",
        ];
    }
}
