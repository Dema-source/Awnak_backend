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
        $user = request()->user();
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:10240'], // Max 10MB
        ];

        // For super admins and system admins, require documentable_id
        if ($user && ($user->hasRole('super_administrator') || $user->hasRole('system_admin'))) {
            $rules['documentable_id'] = ['required', 'integer'];
       }

        return $rules;
    }
}
