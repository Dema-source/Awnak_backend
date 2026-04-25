<?php

namespace App\Http\Requests\Api\Skill;

use Illuminate\Foundation\Http\FormRequest;

class SkillByIdsRequest extends FormRequest
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
            'skill_ids.*' => 'required|integer|exists:skills,id',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ];
    }

    /**
     * Get the validated skill IDs.
     *
     * @return array
     */
    public function getSkillIds(): array
    {
        return $this->validated()['skill_ids'];
    }

    /**
     * Get the per page value.
     *
     * @return int
     */
    public function getPerPage(): int
    {
        return (int) $this->input('per_page', 15);
    }
}
