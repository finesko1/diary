<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class AssignmentTypeDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && !(auth()->user()->isLearner());
    }

    public function prepareForValidation()
    {
        $this->merge([
           'assignment_type_id' => $this->route('assignmentTypeId')
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
            'assignment_type_id' => 'required|integer|exists:assignment_types,id',
        ];
    }

    public function messages(): array
    {
        return [
            'assignment_type_id.required' => 'Номер вида задания обязателен',
            'assignment_type_id.integer' => 'Номер вида задания должен быть в числовом формате',
            'assignment_type_id.exists' => 'Записи с текущим номером вида задания не существует',
        ];
    }
}
