<?php

namespace App\Http\Requests\UserTopic;

use App\Models\Subject\UserTopic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RemoveAttachmentInUserTopicDeleteRequest extends FormRequest
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
            'attachment_id' => [
                'required',
                'integer',
                Rule::exists('files', 'id')->where(function ($query) {
                    return $query->where('attachable_id', $this->route('userTopicId'))
                        ->where('attachable_type', UserTopic::class);
                })
            ]
        ];
    }
}
