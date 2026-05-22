<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMessageRequest extends FormRequest
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
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'ad_id' => ['required', 'integer', 'exists:products,id'],
            'text' => ['required', 'string', 'min:1', 'max:5000'],
            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => ['string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Поле user_id обязательно для заполнения.',
            'user_id.integer' => 'Поле user_id должно быть числом.',
            'user_id.exists' => 'Пользователь с указанным ID не найден.',
            'ad_id.required' => 'Поле ad_id обязательно для заполнения.',
            'ad_id.integer' => 'Поле ad_id должно быть числом.',
            'ad_id.exists' => 'Продукт/объявление с указанным ID не найдено.',
            'text.required' => 'Поле text обязательно для заполнения.',
            'text.string' => 'Поле text должно быть строкой.',
            'text.min' => 'Минимальная длина сообщения - 1 символ.',
            'text.max' => 'Максимальная длина сообщения - 5000 символов.',
            'files.array' => 'Поле files должно быть массивом.',
            'files.max' => 'Максимальное количество файлов - 10.',
            'files.*.string' => 'Каждый файл должен быть строкой (URL или путь).',
            'files.*.max' => 'Длина пути к файлу не должна превышать 500 символов.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'пользователь',
            'ad_id' => 'объявление',
            'text' => 'сообщение',
            'files' => 'файлы',
        ];
    }
}
