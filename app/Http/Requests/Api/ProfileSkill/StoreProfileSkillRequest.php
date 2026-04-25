<?php

namespace App\Http\Requests\Api\ProfileSkill;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileSkillRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'profile_id' => 'required|integer|exists:profiles,id',
            'skill_id' => 'required|integer|exists:skills,id|unique:profile_skill,skill_id,NULL NULL,profile_id,' . $this->profile_id,
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'profile_id.required' => 'The profile ID is required.',
            'profile_id.exists' => 'The selected profile does not exist.',
            'skill_id.required' => 'The skill ID is required.',
            'skill_id.exists' => 'The selected skill does not exist.',
            'skill_id.unique' => 'This skill is already attached to the profile.',
        ];
    }
}
