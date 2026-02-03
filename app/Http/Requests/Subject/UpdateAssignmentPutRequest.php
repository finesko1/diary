<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignmentPutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && !(auth()->user()->isLearner());
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'lesson_id' => $this->route('lessonId'),
            'user_topic_id' => $this->route('userTopicId'),
            'assignment_id' => $this->route('assignmentId'),
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
            'lesson_id' => 'required|integer|exists:lessons,id',
            'user_topic_id' => 'required|integer|exists:user_topics,id',
            'assignment_id' => 'required|integer|exists:assignments,id',
            'assignment_type_id' => 'required|integer|exists:assignment_types,id',
            'description' => 'nullable|string',
            'mark' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' =>  'Укажите номер задания',
            'user_id.required' =>  'Укажите id пользователя',

            'id.integer' =>  'Необходим числовой формат',
            'type_id.integer' =>  'Необходим числовой формат',

            'id.exists' => 'Задания не существует',
            'type_id.exists' => 'Типа задания не существует',
            'user_id.exists' => 'Пользователя не существует',
        ];
    }
}
