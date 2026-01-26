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
            '*.date' => 'required|date',
            '*.time' => 'required|date_format:H:i',
            '*.subject_id' => 'required|integer|exists:subjects,id',
            '*.user_id' => 'required|uuid|exists:users,id',
            '*.topic_id' => 'required_without:*.topic_name|integer|exists:topics,id',
            '*.topic_name' => 'required_without:*.topic_id|string',
            '*.topic_description' => 'string',
            '*.assignments' => 'array',
            '*.assignments.*.assignment_id' => 'required_without:*.assignments.*.assignment_name|
                exists:assignments,id',
            '*.assignments.*.assignment_name' => 'required_without:*.assignments.*.assignment_id|string',
            '*.assignments.*.assignment_description' => 'string'
        ];
    }

    public function messages(): array
    {
        return [
            '*.date.required' => "Укажите дату занятия",
            '*.date.date' => "Укажите дату занятия в формате дд-мм-гггг",

            '*.time.required' => "Укажите время занятия",
            '*.time.date_format' => "Укажите время в формате часы:минуты",
            '*.time.time' => "Укажите время в формате часы:минуты",

            '*.subject_id.required' => 'Укажите предмет занятия',
            '*.subject_id.exists' => 'Выберите существующий предмет',
            '*.subject_id.integer' => 'Укажите номер предмета',

            '*.user_id.required' => 'Выберите ученика',
            '*.user_id.uuid' => 'Неправильный формат uuid ученика',
            '*.user_id.exists' => 'Ученика не существует',

            '*.topic_id.integer' => 'Укажите номер темы',
            '*.topic_id.exists' => 'Темы не существует',

            '*.assignments.array' => 'Формат создания заданий в виде массива',
            '*.assignments.*.assignment_id.required' => 'Укажите тип задания',
            '*.assignments.*.assignment_id.exists' => 'Данного типа задания не существует',
            '*.assignments.*.assignment_name.required' => 'Укажите тип задания',
        ];
    }
}
