<?php

namespace App\Http\Requests\Image;

use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules()
    {
        return [
            'image' => 'required|image',
            'imagetable_id' => 'required',
            'image_type' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'поле текст комментария обязательно к заполнению',
            'imagetable_id.required' => 'поле идентификатор комментируемого объекта обязательно к заполнению',
            'image_type.required' => 'поле тип комментируемого объекта обязательно к заполнению',
            'image.image' => 'поле должно быть изображением',
        ];
    }
}
