<?php

namespace App\Http\Requests\Api\Profile;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
        $user = auth()->user();
        $isAdmin = $user && ($user->hasRole('super_administrator') || $user->hasRole('system_admin'));
        
        $rules = [
            'bio'       => ['nullable', 'string', 'max:500'],
            'age'       => ['nullable', 'integer', 'min:1', 'max:120'],
            'gender'    => ['sometimes', 'string', 'in:male,female'],
            'interests' => ['nullable', 'array'],
            'interests.*' => ['string', 'max:40'],
        ];
        
        // Add user_id validation for admin users (optional for updates)
        if ($isAdmin) {
            $rules['user_id'] = ['sometimes', 'integer', 'exists:users,id'];
        } else {
            // Regular users cannot specify user_id
            $rules['user_id'] = ['prohibited'];
        }
        
        return $rules;
    }
}
