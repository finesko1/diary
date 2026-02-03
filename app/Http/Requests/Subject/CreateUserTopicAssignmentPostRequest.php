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
            'lesson_id' => $this->route('lessonId'),
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
            'lesson_id' => 'required|integer|exists:lessons,id',
            'user_topic_id' => 'required|exists:user_topics,id',
            'assignment_id' => 'nullable|exists:user_topic_assignments,id',
        ];
    }

    public function messages(): array
    {
        return [
        ];
    }
}
