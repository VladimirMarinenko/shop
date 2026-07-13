<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:20',
            'comment'       => 'nullable|string|max:500',
            'delivery_date' => 'nullable|date|after:now',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Имя обязательно для заполнения.',
            'email.required'         => 'Email обязателен для заполнения.',
            'email.email'            => 'Введите корректный email.',
            'phone.required'         => 'Телефон обязателен для заполнения.',
            'delivery_date.after'    => 'Дата доставки должна быть в будущем.',
        ];
    }
}
