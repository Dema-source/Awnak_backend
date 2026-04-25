<?php

namespace App\Http\Requests\Api\ProfileSkill;

use Illuminate\Foundation\Http\FormRequest;

class AttachSkillsRequest extends FormRequest
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
            'skill_ids' => 'required|array|min:1',
            'skill_ids.*' => 'integer|exists:skills,id',
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
            'skill_ids.required' => 'The skill IDs array is required.',
            'skill_ids.array' => 'The skill IDs must be an array.',
            'skill_ids.min' => 'At least one skill ID must be provided.',
            'skill_ids.*.integer' => 'Each skill ID must be an integer.',
            'skill_ids.*.exists' => 'One or more selected skills do not exist.',
        ];
    }
}
