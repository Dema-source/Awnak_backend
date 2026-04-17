<?php

namespace App\Http\Requests\Api\Country;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCountryRequest extends FormRequest
{
    /**
     * Determine if user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $countryId = $this->route('country');
        
        return [
            'name' => ['sometimes', 'string', 'max:100', 'unique:countries,name,' . $countryId],
            'code' => ['sometimes', 'string', 'max:3', 'unique:countries,code,' . $countryId],
            'dialing_code' => ['nullable', 'string', 'max:10'],
            'currency' => ['nullable', 'string', 'max:3'],
            'capital' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'subregion' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'decimal:8'],
            'longitude' => ['nullable', 'decimal:8'],
            'is_active' => ['boolean'],
        ];
    }

}
