<?php

namespace App\Http\Requests\PersonalData;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDateOfBirthPostRequest extends FormRequest
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
            'dateOfBirth' => 'date_format:d-m-Y|nullable'
        ];
    }

    public function messages(): array
    {
        return [
            'dateOfBirth.date_format' => 'Формат даты дд-мм-гггг'
        ];
    }
}
