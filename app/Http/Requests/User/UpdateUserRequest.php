<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user');
         return [
             'name' => 'string|min:5|max:50',
             'email' => 'email|unique:users,email|min:10|max:50' . $userId,
             'password' => 'string|min:4|max:40',
         ];
     }
 
     public function messages(): array
     {
         return [
             'name.string' => 'Можно использовать только символы',
             'name.min' => 'Минимальное колличество символов должно быть 5',
             'name.max' => 'Максимальное колличество символов должно быть 50',
 
             'email.email' => 'Введите валидный Email',
             'email.unique' => 'Это поле должо быть уникальным',
             'email.min' => 'Минимальное колличество символов должно быть 10',
             'email.max' => 'Максимальное колличество символов должно быть 50',
 
             'password.min' => 'Минимальное колличество символов должно быть 4',
             'password.max' => 'Максимальное колличество символов должно быть 40',
         ];
     }
}
