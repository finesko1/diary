<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class CreateAssignmentPostRequest extends FormRequest
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
            'assignment_type_id' => 'required|integer|exists:assignments,id',
            'assignment_description' => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'assignment_type_id.required' => 'Укажите предмет',
            'assignment_type_id.integer' => 'Укажите номер предмета',
            'assignment_type_id.exists' => 'Вид задания не существует',
        ];
    }
}
