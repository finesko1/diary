<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class AssignmentDeleteRequest extends FormRequest
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
            'lesson_id' => 'required|exists:lessons,id',
            'user_topic_id' => 'required|exists:user_topics,id',
            'assignment_id' => 'required|exists:assignments,id',
        ];
    }
}
