<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['new', 'processing', 'completed', 'cancelled'])],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Статус обязателен для выбора.',
            'status.in' => 'Выбран недопустимый статус.',
        ];
    }
}
