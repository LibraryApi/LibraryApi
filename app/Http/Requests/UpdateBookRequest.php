<?php

namespace App\Http\Requests;

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
            "title" => "required|max:255|min:10|unique:books,title",
            "author" => "required|max:255|min:3",
            "description" => "min:10|max:1000|required",
        ];
    }
    public function messages(): array
    {
        return [
            "required" => "это поле :attribute обязательно к заполнению",
            "unique" => ":attribute должно быть уникальным"
        ];
    }
}
