<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserTopicAssignmentPostRequest extends FormRequest
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
            'subject_id' => $this->route('subjectId'),
            'user_topic_id' => $this->route('userTopicId'),
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
            'user_topic_id' => 'required|exists:user_topics,id',
            'assignment_id' => 'required|exists:assignments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'subject_id.required' => 'Укажите предмет занятия',
            'subject_id.exists' => 'Выберите существующий предмет',
            'subject_id.integer' => 'Укажите номер предмета',
            'user_topic_id.required' => 'Укажите номер занятия пользователя',
            'user_topic_id.exists' => 'Занятия для пользователя не существует',
            'assignment_id.required' => 'Укажите задание',
            'assignment_id.exists' => 'Задания не существует',
        ];
    }
}
