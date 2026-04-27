<?php

namespace App\Http\Requests\Api\Document;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

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
        $user = request()->user();
        $rules = [
            'title' => ['sometimes', 'string', 'max:255'],
            'file' => ['sometimes', 'file', 'max:2048'], // Max 2MB (matching PHP limit)
        ];

        // For super admins and system admins, allow changing documentable ownership
        if ($user && ($user->hasRole('super_administrator') || $user->hasRole('system_admin'))) {
            $rules['documentable_id'] = ['sometimes', 'integer'];
            $rules['documentable_type'] = ['sometimes', 'string', 'in:App\Models\Volunteer,App\Models\Opportunity'];
        }

        return $rules;
    }
}
