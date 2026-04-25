<?php

namespace App\Http\Requests\Api\Document;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:10240'], // Max 10MB
            'documentable_type' => ['required', 'string', 'in:App\Models\Volunteer,App\Models\Opportunity'],
            'documentable_id' => ['required', 'integer'],
        ];
    }
}
