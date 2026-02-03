<?php

namespace App\Http\Requests\Subject;

use App\Models\User\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateSubjectPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && !(auth()->user()->isLearner());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'datetime' => 'required|date',
            'subject_id' => 'required|integer|exists:subjects,id',
            'user_id' => 'required|uuid|exists:users,id',
            'topics' => 'array',
            'topics.topic_id' => 'integer|exists:topics,id',
            'topics.topic_name' => 'string',
            'topics.topic_description' => 'string',
            'topics.assignments' => 'array',
            'topics.assignments.tasks.assignment_id' => 'integer|exists:assignments,id',
            'topics.assignments.tasks.assignment_name' => 'string',
            'topics.assignments.tasks.assignment_description' => 'string'
        ];
    }

    public function messages(): array
    {
        return [
            'datetime.required' => "Укажите дату занятия",
            'datetime.date' => "Укажите дату занятия в формате ISO",

            'subject_id.required' => 'Укажите предмет занятия',
            'subject_id.exists' => 'Выберите существующий предмет',
            'subject_id.integer' => 'Укажите номер предмета',

            'user_id.required' => 'Выберите ученика',
            'user_id.uuid' => 'Неправильный формат uuid ученика',
            'user_id.exists' => 'Ученика не существует',

            'topics.array' => 'Формат создания тем в виде массива',
            'topics.topic_id.integer' => 'Укажите номер темы',
            'topics.topic_id.exists' => 'Темы не существует',

            'topics.assignments.array' => 'Формат создания заданий в виде массива',
            'topics.assignments.tasks.assignment_id.required' => 'Укажите тип задания',
            'topics.assignments.tasks.assignment_id.exists' => 'Данного типа задания не существует',
            'topics.assignments.tasks.assignment_name.required' => 'Укажите тип задания',
        ];
    }
}
