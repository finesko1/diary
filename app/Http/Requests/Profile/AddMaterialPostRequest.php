<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class AddMaterialPostRequest extends FormRequest
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
            'user_id' => $this->route('userId'),
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
            'subject_id' => 'required|exists:subjects,id',
            'user_id' => 'required|uuid|exists:users,id',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,txt,jpeg,jpg,png,gif,webp,mp4,avi,mov,wmv,webm|max:204800',
        ];
    }
}
