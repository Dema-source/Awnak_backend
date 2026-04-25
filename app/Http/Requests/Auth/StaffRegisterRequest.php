<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StaffRegisterRequest extends FormRequest
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
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', 'exists:skills,id'],
        ];
    }

    /**
     * Get the validated data for user, profile, and skills creation.
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
            ],
            'profile' => [
                'bio' => $validated['bio'] ?? null,
                'age' => $validated['age'],
                'gender' => $validated['gender'],
                'interests' => $validated['interests'] ?? [],
            ],
            'skills' => $validated['skills'] ?? [],
        ];
    }
}
