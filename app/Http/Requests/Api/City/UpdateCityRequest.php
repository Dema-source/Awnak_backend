<?php

namespace App\Http\Requests\Api\City;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCityRequest extends FormRequest
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
        $cityId = $this->route('city');
        
        return [
            'country_id' => ['sometimes', 'integer', 'exists:countries,id'],
            'name' => ['sometimes', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'decimal:8'],
            'longitude' => ['nullable', 'decimal:8'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom error messages for validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'country_id.integer' => 'The country must be an integer.',
            'country_id.exists' => 'The selected country does not exist.',
            'name.max' => 'The city name must not exceed 100 characters.',
            'state.max' => 'The state name must not exceed 100 characters.',
            'latitude.decimal' => 'The latitude must be a decimal number.',
            'longitude.decimal' => 'The longitude must be a decimal number.',
            'postal_code.max' => 'The postal code must not exceed 20 characters.',
        ];
    }
}
