<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
    public function rules(): array
    {
        $routeUser = $this->route('user');
        $userId = is_object($routeUser) ? $routeUser->id : $routeUser;

        return [
            'name' => ['sometimes', 'string', 'min:2', 'max:50'],
            'email' => [
                'sometimes',
                'email',
                'max:50',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => ['sometimes', 'nullable', 'string', 'min:4', 'max:40', 'confirmed'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'city' => ['nullable', 'string', 'max:100'],
            'telegram' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'github' => ['nullable', 'string', 'max:100'],
            'vk' => ['nullable', 'string', 'max:100'],
            'habr' => ['nullable', 'string', 'max:100'],
            'about' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'banner_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:6144'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Имя должно быть текстом.',
            'name.min' => 'Имя должно быть не короче 2 символов.',
            'name.max' => 'Имя должно быть не длиннее 50 символов.',

            'email.email' => 'Введите корректный email.',
            'email.unique' => 'Этот email уже используется.',
            'email.max' => 'Email должен быть не длиннее 50 символов.',

            'password.min' => 'Пароль должен быть не короче 4 символов.',
            'password.max' => 'Пароль должен быть не длиннее 40 символов.',
            'password.confirmed' => 'Пароли не совпадают.',

            'birth_date.date' => 'Введите корректную дату рождения.',
            'birth_date.before' => 'Дата рождения должна быть в прошлом.',

            'city.max' => 'Название города слишком длинное.',
            'telegram.max' => 'Telegram слишком длинный.',
            'phone.max' => 'Телефон слишком длинный.',
            'github.max' => 'GitHub слишком длинный.',
            'vk.max' => 'VK слишком длинный.',
            'habr.max' => 'Habr слишком длинный.',
            'about.max' => 'Описание профиля должно быть не длиннее 1000 символов.',

            'avatar.image' => 'Аватар должен быть изображением.',
            'avatar.mimes' => 'Аватар должен быть в формате jpeg, png, jpg, gif или webp.',
            'avatar.max' => 'Размер аватара не должен превышать 4 МБ.',

            'banner_image.image' => 'Баннер должен быть изображением.',
            'banner_image.mimes' => 'Баннер должен быть в формате jpeg, png, jpg, gif или webp.',
            'banner_image.max' => 'Размер баннера не должен превышать 6 МБ.',
        ];
    }
}
