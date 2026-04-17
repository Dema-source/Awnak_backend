<?php

namespace App\Http\Requests\Api\City;

use Illuminate\Foundation\Http\FormRequest;

class WithinRadiusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
        return [
            'latitude' => 'required|decimal:8',
            'longitude' => 'required|decimal:8',
            'radius' => 'required|integer|min:1|max:1000'
        ];
    }
}
