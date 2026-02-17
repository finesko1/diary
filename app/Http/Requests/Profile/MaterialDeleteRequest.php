<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class MaterialDeleteRequest extends FormRequest
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
            'profile_material_id' => $this->route('profileMaterialId'),
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
            'user_id' => 'required|uuid|exists:users,id',
            'profile_material_id' => 'required|integer|exists:profile_materials,id',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Укажите пользователя',
            'user_id.uuid' => 'Формат пользователя неверный',
            'user_id.exists' => 'Пользователя не существует',

            'profile_material_id.required' => 'Укажите номер материала профиля',
            'profile_material_id.integer' => 'Номер должен быть в числовом формате',
            'profile_material_id.exists' => 'Материала не существует',
        ];
    }
}
