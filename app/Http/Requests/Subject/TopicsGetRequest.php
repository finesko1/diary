<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class TopicsGetRequest extends FormRequest
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
           'subject_id' => $this->route('subjectId'),
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
            'subject_id' => 'required|integer|exists:subjects,id',
        ];
    }

    public function messages(): array
    {
        return [
            'subject_id.required' => 'Предмет не указан',
            'subject_id.integer' => 'Номер предмета должен быть числом',
            'subject_id.exists' => 'Предмет не найден',
        ];
    }
}
