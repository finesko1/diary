<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserTopicPutRequest extends FormRequest
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
            'subject_id' => $this->route('subjectId'),
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
            'id' => 'required|integer|exists:user_topics,id',
            'topic_id' => 'nullable|integer|exists:topics,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'topic_name' => 'required_without:topic_id|string',
            'topic_description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Укажите номер занятия',
            'id.integer' => 'Формат номера занятия - число',
            'id.exists' => 'Занятия не существует',

            'topic_id.integer' => 'Формат типа занятия - число',
            'topic_id.exists' => 'Тип занятия не существует',

            'subject_id.required' => 'Укажите номер предмета',
            'subject_id.integer' => 'Номер предмета должен быть в числовом формате',
            'subject_id.exists' => 'Предмета не существует',

            'topic_name.required_without' => 'Укажите тип нового занятия',
        ];
    }
}
