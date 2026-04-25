<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class VolunteerRegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'integer'],
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'array'],
            'address.en' => ['required', 'string', 'max:500'],
            'address.ar' => ['nullable', 'string', 'max:500'],
            'age' => ['required', 'integer', 'min:18', 'max:120'],
            'gender' => ['required', 'string', 'in:male,female'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'interests' => ['nullable', 'array'],
            'interests.*' => ['string', 'max:255'],
            'languages' => ['nullable', 'array', 'max:5'],
            'languages.*' => ['string', 'in:Arabic,English,Spanish,French,Portuguese,German,Italian,Dutch,Russian,Chinese,Japanese,Korean,Vietnamese,Thai,Indonesian,Malay,Turkish,Persian,Hebrew,Hindi,Urdu,Bengali,Punjabi,Amharic,Swahili,French Canadian,Brazilian Portuguese,Mexican Spanish,Australian English'],
            'availability' => ['nullable', 'array', 'min:1', 'max:7'],
            'availability.*.days' => ['required', 'string', 'regex:/^((sunday|monday|tuesday|wednesday|thursday|friday|saturday)(-(sunday|monday|tuesday|wednesday|thursday|friday|saturday))?)(,(sunday|monday|tuesday|wednesday|thursday|friday|saturday)(-(sunday|monday|tuesday|wednesday|thursday|friday|saturday))?)*/i'],
            'availability.*.from' => ['required', 'string', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'availability.*.to' => ['required', 'string', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', 'exists:skills,id'],
            'experience_years' => ['nullable', 'string', 'in:1,2,3,4,5'],
        ];
    }

    /**
     * Get the validated data for user, profile, skills, and volunteer creation.
     *
     * @return array
     */
    public function getValidatedData(): array
    {
        $validated = $this->validated();

        return [
            'user' => [
                'name' => $validated['name']['en'], // Use English name as primary
                'email' => $validated['email'],
                'password' => $validated['password'],
                'phone' => $validated['phone'],
                'address' => $validated['address']['en'], // Use English address as primary
                'role' => 'volunteer',
            ],
            'profile' => [
                'bio' => $validated['bio'] ?? null,
                'age' => $validated['age'],
                'gender' => $validated['gender'],
                'interests' => $validated['interests'] ?? [], // Interests from request
            ],
            'volunteer' => [
                'languages' => $validated['languages'] ?? [],
                'availability' => $validated['availability'] ?? [],
                'experience_years' => $validated['experience_years'] ?? '2', // Experience from request
            ],
            'skills' => $validated['skills'] ?? [], // Skills array from request
        ];
    }
}
