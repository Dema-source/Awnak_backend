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
        return [
            'bio'       => ['nullable', 'string', 'max:500'],
            'age'       => ['nullable', 'integer', 'min:1', 'max:120'],
            'gender'    => ['sometimes', 'string', 'in:male,female'],
            'interests' => ['nullable', 'array'],
            'interests.*' => ['string', 'max:40'],
        ];
    }
}
