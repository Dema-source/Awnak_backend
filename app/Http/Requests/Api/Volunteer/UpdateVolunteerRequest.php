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
        $user = request()->user();
        $isAdmin = $user && ($user->hasRole('super_administrator') || $user->hasRole('system_admin'));
        
        $rules = [
            'languages' => ['nullable', 'array', 'max:5'],
            'languages.*' => ['string', 'in:Arabic,English,Spanish,French,Portuguese,German,Italian,Dutch,Russian,Chinese,Japanese,Korean,Vietnamese,Thai,Indonesian,Malay,Turkish,Persian,Hebrew,Hindi,Urdu,Bengali,Punjabi,Amharic,Swahili,French Canadian,Brazilian Portuguese,Mexican Spanish,Australian English'],
            'availability' => ['nullable', 'array', 'min:1', 'max:7'],
            'availability.*.days' => ['required', 'string', 'regex:/^((sunday|monday|tuesday|wednesday|thursday|friday|saturday)(-(sunday|monday|tuesday|wednesday|thursday|friday|saturday))?)(,(sunday|monday|tuesday|wednesday|thursday|friday|saturday)(-(sunday|monday|tuesday|wednesday|thursday|friday|saturday))?)*/i'],
            'availability.*.from' => ['required', 'string', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'availability.*.to' => ['required', 'string', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'experience_years' => ['sometimes', 'string', 'in:1,2,3,4,5'],
        ];

        // Support both complex structure and raw array format
        // Check if availability is raw array (for translation) or complex structure
        if (request()->has('availability')) {
            $availability = request()->input('availability');
            
            // If it's a simple array (raw format for translation), use simple validation
            if (is_array($availability) && isset($availability[0]) && is_string($availability[0])) {
                // Raw array format - simple validation
                $rules['availability'] = ['nullable', 'array', 'min:1', 'max:7'];
                $rules['availability.*'] = ['string', 'in:weekends,weekdays,mornings,evenings,afternoons,nights'];
            }
            // If it's complex structure with days/from/to, keep complex validation
        }

        // Support both simple array and complex structure for languages
        if (request()->has('languages')) {
            $languages = request()->input('languages');
            
            // If it's a simple array of strings, allow any language values
            if (is_array($languages) && isset($languages[0]) && is_string($languages[0])) {
                // Raw array format - more permissive validation for translation
                $rules['languages.*'] = ['string', 'in:Arabic,English,Spanish,French,Portuguese,German,Italian,Dutch,Russian,Chinese,Japanese,Korean,Vietnamese,Thai,Indonesian,Malay,Turkish,Persian,Hebrew,Hindi,Urdu,Bengali,Punjabi,Amharic,Swahili,French Canadian,Brazilian Portuguese,Mexican Spanish,Australian English'];
            }
        }

        // Admin-specific rules
        if ($isAdmin) {
            $rules['profile_id'] = ['sometimes', 'integer', 'exists:profiles,id'];
            $rules['status'] = ['sometimes', 'string', 'in:active,In_active,pending,blocked'];
        }

        return $rules;

        // Admin-specific rules
        if ($isAdmin) {
            $rules['profile_id'] = ['sometimes', 'integer', 'exists:profiles,id'];
            // $rules['status'] = ['sometimes', 'string', 'in:active,In_active,pending,blocked'];
        }

        return $rules;
    }
}
