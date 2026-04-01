<?php

namespace App\Http\Requests\Api\Opportunity;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOpportunityRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:100'],
            'expected_duration' => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date_format:Y-m-d', 'date', 'before_or_equal:end_date', 'after_or_equal:today'],
            'end_date' => ['required', 'date_format:Y-m-d', 'date', 'after_or_equal:start_date'],
            'required_volunteers' => ['required', 'integer', 'min:1'],
        ];
    }
}
