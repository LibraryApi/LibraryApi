<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'min:5', 'max:255'],
            'price' => ['sometimes', 'numeric', 'min:0', 'max:10000'],
            'duration_months' => ['sometimes', 'integer', 'min:1', 'max:255'],
            'access_level' => ['sometimes', 'nullable', 'in:base,premium'],
        ];
    }
}
