<?php

namespace App\Http\Requests\book;

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
        ];
    }
    public function messages(): array
    {
        return [
            "required" => "это поле :attribute обязательно к заполнению",
            "unique" => ":attribute должно быть уникальным",

            "author.max" => "Максимальное колличество символов должно быть 50",
            "author.min" => "Минимальное колличество символов должно быть 5",

            "title.max" => "Максимальное колличество символов должно быть 50",
            "title.min" => "Минимальное колличество символов должно быть 5",

            "description.max" => "Максимальное колличество символов должно быть 10000",
            "description.min" => "Минимальное колличество символов должно быть 10",
        ];
    }
}
