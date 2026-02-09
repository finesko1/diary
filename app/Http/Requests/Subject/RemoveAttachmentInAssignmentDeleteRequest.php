<?php

namespace App\Http\Requests\Subject;

use App\Models\Subject\Assignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RemoveAttachmentInAssignmentDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'lesson_id' => $this->route('lessonId'),
            'user_topic_id' => $this->route('userTopicId'),
            'assignment_id' => $this->route('assignmentId'),
            'attachment_id' => $this->route('attachmentId'),
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
            'attachment_id' => [
                'required',
                'integer',
                Rule::exists('files', 'id')->where(function ($query) {
                    return $query->where('attachable_id', $this->route('assignmentId'))
                        ->where('attachable_type', Assignment::class);
                })
            ]
        ];
    }
}
