<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:4',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Вы должны указать свое имя',
            'name.string' => 'Можно использовать только символы',

            'email.required' => 'Вы должны указать свой email',
            'email.email' => 'Данное поле должно быть email',
            'email.unique' => 'Это поле должо быть уникальным',

            'password.required' => 'Вы должны указать свой пароль',
            'password.min' => 'Минимальное колличество символов должно быть 4',
        ];
    }
}
