<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class AddAttachmentInAssignmentPostRequest extends FormRequest
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
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,txt,jpeg,jpg,png,gif,webp,mp4,avi,mov,wmv,webm|max:204800',
        ];
    }
}
