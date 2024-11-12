<?php

return [
    'required' => 'Поле :attribute обязательно для заполнения.',
    'string' => 'Поле :attribute должно быть строкой.',
    'min' => [
        'string' => 'Поле :attribute должно содержать не менее :min символов.',
        'numeric' => 'Поле :attribute должно быть больше или равно :min.',
        'array' => 'Поле :attribute должно содержать как минимум :min элемента.',
    ],
    'max' => [
        'string' => 'Поле :attribute не должно превышать :max символов.',
        'numeric' => 'Поле :attribute не должно превышать :max.',
        'array' => 'Поле :attribute не должно содержать больше :max элементов.',
    ],
    'integer' => 'Поле :attribute должно быть целым числом.',
    'numeric' => 'Поле :attribute должно быть числовым значением.',
    'nullable' => 'Поле :attribute может быть пустым.',
    'in' => 'Поле :attribute должно быть одним из следующих значений: :values.',
    'attributes' => [
        'name' => 'Название',
        'price' => 'Цена',
        'duration_months' => 'Продолжительность в месяцах',
        'access_level' => 'Уровень доступа',
    ],
];
