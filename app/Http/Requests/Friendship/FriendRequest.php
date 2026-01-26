<?php

namespace App\Http\Requests\Friendship;

use Illuminate\Foundation\Http\FormRequest;

class FriendRequest extends FormRequest
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
            'friendId' => $this->route('friendId'),
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
            'friendId' => 'uuid|required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'friendId.uuid' => 'Неверный формат uuid',
            'friendId.required' => 'ID пользователя обязателен',
            'friendId.exists' => 'Пользователь не найден',
        ];
    }
}
