<?php

namespace App\Http\Requests\Api\Volunteer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationRule;

class StoreVolunteerRequest extends FormRequest
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
        $isAdmin = $user && ($user->hasRole('super_administrator') || $user->hasRole('system_admin'));
        
        $rules = [
            'languages' => ['nullable', 'array', 'max:5'],
            'languages.*' => ['string'],
            'availability' => ['nullable', 'array', 'min:1', 'max:7'],
            'availability.*.days' => ['required', 'string'],
            'availability.*.from' => ['required', 'string', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'availability.*.to' => ['required', 'string', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'experience_years' => ['nullable', 'string', 'in:1,2,3,4,5'],
        ];

        // Admin-specific rules
        if ($isAdmin) {
            $rules['profile_id'] = ['required', 'integer', 'exists:profiles,id'];
            $rules['status'] = ['sometimes', 'string', 'in:active,In_active,pending,blocked'];
        }

        return $rules;
    }
}
