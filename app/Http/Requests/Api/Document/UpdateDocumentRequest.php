<?php

namespace App\Http\Requests\Api\Document;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'file' => ['sometimes', 'file', 'max:10240'], // Max 10MB
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.string' => 'Document title must be a string.',
            'title.max' => 'Document title may not be greater than 255 characters.',
            'file.file' => 'The uploaded file must be a file.',
            'file.max' => 'The file may not be greater than 10MB.',
        ];
    }
}
