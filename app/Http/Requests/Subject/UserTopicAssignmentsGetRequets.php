<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class UserTopicAssignmentsGetRequets extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function prepareForValidation()
    {
        return $this->merge([
            'user_topic_id' => request('userTopicId'),
            'lesson_id' => request('lessonId'),
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
            'user_topic_id' => 'required|integer|exists:user_topics,id',
            'lesson_id' => 'required|integer|exists:lessons,id',
        ];
    }

    public function messages(): array
    {
        return [
            'user_topic_id.required' => 'Укажите номер занятия',
            'user_topic_id.integer' => 'Укажите номер занятия в числовом формате',
            'user_topic_id.exists' => 'Занятия не существует',
        ];
    }
}
