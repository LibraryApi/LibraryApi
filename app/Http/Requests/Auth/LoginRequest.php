<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|email|min:10|max:50',
            'password' => 'required|string|min:4|max:40',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Вы должны указать свой email',
            'email.email' => 'Введите валидный Email',
            'email.min' => 'Минимальное колличество символов должно быть 10',
            'email.max' => 'Максимальное колличество символов должно быть 50',

            'password.required' => 'Вы должны указать свой пароль',
            'password.min' => 'Минимальное колличество символов должно быть 4',
            'password.max' => 'Максимальное колличество символов должно быть 40',
        ];
    }
}
