<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserTopicPostRequest extends FormRequest
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
            'subject_id' => $this->route('subjectId')
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
            'student_id' => 'required|uuid|exists:users,id',
            'topic_id' => 'required|integer|exists:topics,id',
            'datetime' => 'required|date_format:d-m-Y H:i',
        ];
    }

    public function messages(): array
    {
        return [
            'subject_id.required' => 'Укажите предмет',
            'subject_id.exists' => 'Предмета не существует',
            'student_id.required' => 'Укажите пользователя',
            'student_id.exists' => 'Пользователя не существует',
            'topic_id.required' => 'Укажите занятие',
            'topic_id.exists' => 'Занятия не существует',
            'datetime.required' => 'Укажите дату занятия',
            'datetime.date_format' => 'Формат даты занятия день-месяц-год часы:минуты',
        ];
    }
}
