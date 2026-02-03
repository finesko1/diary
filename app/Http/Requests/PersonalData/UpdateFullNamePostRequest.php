<?php

namespace App\Http\Requests\PersonalData;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFullNamePostRequest extends FormRequest
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
            'lastName' => 'string|required',
            'firstName' => 'string|required',
            'middleName' => 'string|required',
        ];
    }

    public function messages(): array
    {
        return [
            'lastName.required' => 'Пожалуйста, укажите вашу фамилию.',
            'lastName.string' => 'Фамилия должна содержать только буквы.',

            'firstName.required' => 'Пожалуйста, укажите ваше имя.',
            'firstName.string' => 'Имя должно содержать только буквы.',

            'middleName.required' => 'Пожалуйста, укажите ваше отчество.',
            'middleName.string' => 'Отчество должно содержать только буквы.',
        ];
    }
}
