<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class MonthEventsGetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'date' => $this->route('date'),
        ]);

        if ($this->query('userId')) {
            $this->merge(['user_id' => $this->query('userId')]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'nullable|uuid|exists:users,id',
            'date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.uuid' => 'Неверный формат id пользователя',
            'user_id.exists' => 'Пользователя не существует',
            'date.date' => 'Неверный формат даты'
        ];
    }
}
