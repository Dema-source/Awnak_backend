<?php

namespace App\Http\Requests\Api\OrganizationProfile;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
        $rules =  [
            'license_number' => ['required', 'string', 'max:255', 'unique:organization_profiles,license_number'],
            'type' => ['required', 'string', 'in:Charitable organization,Civil society organization,Voluntary educational/university institution,Hospital,Religious organization,Company with a Corporate Social Responsibility (CSR) program,Student club/association,Environmental organization'],
            'bio' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
            'user_id' => ['required', 'exists:users,id'],
        ];
        if (($this->user()->hasRole('super_administrator') || $this->user()->hasRole('system_admin'))) {
            $rules['status'] = 'required|in:active,notactive';
        }
        return $rules;
    }
}
