<?php

namespace App\Http\Requests\Api\OrganizationProfile;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreOrganizationProfileRequest extends FormRequest
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
        $user = Auth::user();
        $isAdmin = $user && ($user->hasRole('super_administrator') || $user->hasRole('system_admin'));
        
        $rules = [
            'license_number' => ['required', 'string', 'max:255', 'unique:organization_profiles,license_number'],
            'type' => ['required', 'string', 'in:Charitable organization,Civil society organization,Voluntary educational/university institution,Hospital,Religious organization,Company with a Corporate Social Responsibility (CSR) program,Student club/association,Environmental organization'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'website' => ['nullable', 'url', 'max:255'],
        ];
        
        // Role-based user_id validation
        if ($isAdmin) {
            $rules['user_id'] = ['required', 'integer', 'exists:users,id'];
            $rules['status'] = ['required', 'string', 'in:active,notactive'];
        } else {
            // Organization admins can only create profile for themselves
            $rules['user_id'] = ['prohibited'];
        }
        
        return $rules;
    }
}
