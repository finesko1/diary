<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UploadPhotoPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'photo' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,webp,svg',
                'max:5120',
                'dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'photo.required' => 'Пожалуйста, выберите файл для загрузки',
            'photo.image' => 'Файл должен быть изображением',
            'photo.mimes' => 'Поддерживаемые форматы: JPEG, PNG, JPG, GIF, WEBP, SVG',
            'photo.max' => 'Максимальный размер файла: 5 МБ',
            'photo.dimensions' => 'Изображение должно быть от 100x100 до 1000x1000 пикселей',
        ];
    }
}
