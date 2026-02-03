<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class DayEventsGetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function prepareForValidation()
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
            'date' => 'required|date',
            'user_id' => 'uuid|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => 'Укажите дату',
            'date.date' => 'Формат даты дд-мм-гггг',
            'user_id.uuid' => 'Неверный формат id пользователя',
            'user_id.exists' => 'Пользователя не существует',
        ];
    }
}
