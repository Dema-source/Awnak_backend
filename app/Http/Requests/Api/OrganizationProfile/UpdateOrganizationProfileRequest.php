<?php

namespace App\Http\Requests\Api\OrganizationProfile;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateOrganizationProfileRequest extends FormRequest
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
            'license_number' => ['sometimes', 'string', 'max:255', 'unique:organization_profiles,license_number,' . $this->route('id')],
            'type' => ['sometimes', 'string', 'in:Charitable organization,Civil society organization,Voluntary educational/university institution,Hospital,Religious organization,Company with a Corporate Social Responsibility (CSR) program,Student club/association,Environmental organization'],
            'bio' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'website' => ['sometimes', 'nullable', 'url', 'max:255'],
        ];
        
        // Role-based validation
        if ($isAdmin) {
            $rules['status'] = ['sometimes', 'string', 'in:active,notactive'];
            $rules['user_id'] = ['sometimes', 'integer', 'exists:users,id'];
        } else {
            // Organization admins cannot change user_id or status
            $rules['user_id'] = ['prohibited'];
            $rules['status'] = ['prohibited'];
        }
        
        return $rules;
    }
}
