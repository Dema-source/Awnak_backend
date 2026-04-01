<?php

namespace App\Http\Requests\Api\Location;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
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
        'latitude'  => ['required', 'numeric', 'between:-90,90'],
        'longtude' =>  ['required', 'numeric', 'between:-180,180'],
        'address'   => ['required', 'string', 'max:255'],
        'city'      => ['required', 'string', 'max:100'],
        'country'   => ['required', 'string', 'max:100'],
        ];
    }
}
