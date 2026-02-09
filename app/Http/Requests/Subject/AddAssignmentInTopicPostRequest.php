<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class AddAssignmentInTopicPostRequest extends FormRequest
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
            'user_topic_id' => 'required|integer|exists:user_topics,id',
            'assignment_type_id' => 'required|integer|exists:assignment_types,id',
            'description' => 'nullable|string',
            'status' => 'nullable|enum:pending,in_progress,submitted,graded,returned',
            'mark' => 'nullable|string',
        ];
    }
}
