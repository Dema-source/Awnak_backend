<?php

namespace App\Http\Requests\Api\LocationOpportunity;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOpportunityLocationPivotRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'building_name' => 'sometimes|string|max:100',
            'floor_number' => 'sometimes|integer|max:20',
            'apartment_number' => 'sometimes|integer|max:20',
            'landmark' => 'sometimes|string|max:255',
        ];
    }
}
