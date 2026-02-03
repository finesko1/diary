<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateLearnerPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && !(auth()->user()->isLearner());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => 'required|regex:/^[a-zA-Z0-9_]+$/|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Имя пользователя обязательно для заполнения',
            'username.regex' => 'Имя пользователя может состоять из английских букв и цифр',
            'username.unique' => 'Это имя пользователя уже занято',

            'password.required' => 'Пароль обязателен для заполнения',
            'password.string' => 'Пароль должен быть строкой',
            'password.min' => 'Пароль должен содержать минимум :min символов',
            'password.confirmed' => 'Пароли не совпадают',
        ];
    }
}
