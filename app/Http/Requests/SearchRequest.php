<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Разрешаем запрос (можно добавить проверку роли, если нужно).
     */
    public function authorize(): bool
    {
        return true; // Доступно всем
    }

    /**
     * Правила валидации.
     */
    public function rules(): array
    {
        return [
            'q' => 'nullable|string|max:100',
        ];
    }

    /**
     * Подготовка данных перед валидацией.
     */
    protected function prepareForValidation(): void
    {
        // Обрезаем пробелы по краям
        if ($this->has('q')) {
            $this->merge([
                'q' => trim($this->q),
            ]);
        }
    }

    /**
     * Кастомные сообщения об ошибках (опционально).
     */
    public function messages(): array
    {
        return [
            'q.max' => 'Поисковый запрос не должен превышать 100 символов.',
        ];
    }

    /**
     * Получить экранированный запрос для безопасного использования в LIKE.
     */
    public function getEscapedQuery(): string
    {
        $query = $this->input('q', '');
        return addcslashes($query, '%_'); // экранируем %, _ чтобы пользователь не мог управлять wildcard
    }

    /**
     * Проверить, что запрос не пустой после очистки.
     */
    public function hasValidQuery(): bool
    {
        return !empty($this->input('q'));
    }
}