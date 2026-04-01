<?php

namespace App\Http\Requests\Api\Volunteer;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVolunteerRequest extends FormRequest
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
            'languages' => ['nullable', 'array', 'max:5'],
            'languages.*' => ['string', 'in:Arabic,English,Spanish,French,Portuguese,German,Italian,Dutch,Russian,Chinese,Japanese,Korean,Vietnamese,Thai,Indonesian,Malay,Turkish,Persian,Hebrew,Hindi,Urdu,Bengali,Punjabi,Amharic,Swahili,French Canadian,Brazilian Portuguese,Mexican Spanish,Australian English'],
            'availability' => ['nullable', 'array', 'min:1', 'max:7'],
            'availability.*.days' => ['required', 'string', 'regex:/^((sunday|monday|tuesday|wednesday|thursday|friday|saturday)(-(sunday|monday|tuesday|wednesday|thursday|friday|saturday))?)(,(sunday|monday|tuesday|wednesday|thursday|friday|saturday)(-(sunday|monday|tuesday|wednesday|thursday|friday|saturday))?)*/i'],
            'availability.*.from' => ['required', 'string', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'availability.*.to' => ['required', 'string', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'experience_years' => ['sometimes', 'string', 'in:1,2,3,4,5'],
        ];
    }
}
