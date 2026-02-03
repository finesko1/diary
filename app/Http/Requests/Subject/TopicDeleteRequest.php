<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class TopicDeleteRequest extends FormRequest
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
            'topic_id' => $this->route('topicId'),
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
            'topic_id' => 'required|integer|exists:topics,id',
        ];
    }

    public function messages(): array
    {
        return [
            'topic_id.required' => 'Укажите тему предмета',
            'topic_id.integer' => 'Укажите тему предмета в числовом формате',
            'topic_id.exists' => 'Темы не существует',
        ];
    }
}
