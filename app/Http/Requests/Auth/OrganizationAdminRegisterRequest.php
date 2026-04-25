<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class OrganizationAdminRegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'integer'],
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'array'],
            'address.en' => ['required', 'string', 'max:500'],
            'address.ar' => ['nullable', 'string', 'max:500'],
            'organization_profile' => ['required', 'array'],
            'organization_profile.license_number' => ['required', 'string', 'max:255', 'unique:organization_profiles,license_number'],
            'organization_profile.type' => ['required', 'string', 'in:Charitable organization,Civil society organization,Voluntary educational/university institution,Hospital,Religious organization,Company with a Corporate Social Responsibility (CSR) program,Student club/association,Environmental organization'],
            'organization_profile.bio' => ['nullable', 'string'],
            'organization_profile.website' => ['nullable', 'url', 'max:255'],
        ];
    }

    /**
     * Get the validated data for user and organization profile creation.
     *
     * @return array
     */
    public function getValidatedData(): array
    {
        $validated = $this->validated();

        return [
            'user' => [
                'name' => $validated['name']['en'], // Use English name as primary
                'email' => $validated['email'],
                'password' => $validated['password'],
                'phone' => $validated['phone'],
                'address' => $validated['address']['en'], // Use English address as primary
                'role' => 'organization_admin',
            ],
            'organization_profile' => $validated['organization_profile'],
        ];
    }
}
