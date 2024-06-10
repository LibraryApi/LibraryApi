<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
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
            'name' => 'required|string|min:5|max:255',
            'price' => 'required|numeric|min:0|max:10000', // Предполагается, что цена - числовое значение
            'duration_months' => 'required|integer|min:1|max:255', // Предполагается, что длительность - целое число
            'access_level' => 'nullable|in:base,premium', // Предполагается, что доступный уровень - либо "basic", либо "premium"
        ];
    }
}
