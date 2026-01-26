<?php

namespace App\Http\Requests\Friendship;

use Illuminate\Foundation\Http\FormRequest;

class ValidateRequestsGetRequest extends FormRequest
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
        request()->merge([
            'type' => $this->query('type'),
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
            'type' => 'string|in:pending,pending_me,blocked,blocked_me,declined',
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'Возможные варианты: pending, pending_me, declined, blocked, blocked_me',
        ];
    }
}
