<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class CreateTopicPostRequest extends FormRequest
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
        return $this->merge([
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
            'topic_name' => 'string',
            'topic_description' => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'subject_id.required' => 'Укажите предмет занятия',
            'subject_id.exists' => 'Выберите существующий предмет',
            'subject_id.integer' => 'Укажите номер предмета',
        ];
    }
}
