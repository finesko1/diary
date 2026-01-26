<?php

namespace App\Http\Requests\EducationData;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLanguageLevelPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'uuid|required|exists:users,id',
            'language_id' => 'integer|required|exists:subjects,id',
            'level' => 'string|required',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => 'Пользователь не найден',
            'user_id.uuid' => 'Неверный формат ID пользователя',
            'user_id.required' => 'ID пользователя обязателен',
        ];
    }
}
