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
        return [
            'type'    => ['required', 'string', 'in:Charitable organization,Civil society organization,Voluntary educational/university institution,Hospital,Religious organization,Company with a Corporate Social Responsibility (CSR) program,Student club/association,Environmental organization'],
        ];
    }
}
