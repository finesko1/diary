<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class MaterialsGetRequest extends FormRequest
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
            'user_id' => $this->route('userId')
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|uuid|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Укажите пользователя',
            'user_id.uuid' => 'Формат номера пользователя неверен',
            'user_id.exists' => 'Пользователя не существует',
        ];
    }
}
