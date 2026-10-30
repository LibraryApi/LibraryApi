<?php

namespace App\Http\Requests\Export;

use Illuminate\Foundation\Http\FormRequest;

class ExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'entity' => ['required', 'in:books,posts'],
            'report_period' => ['nullable', 'in:week,day,month,custom'],
            'start' => ['nullable', 'date', 'required_if:report_period,custom'],
            'end' => ['nullable', 'date', 'required_if:report_period,custom', 'after_or_equal:start'],
        ];
    }
}
