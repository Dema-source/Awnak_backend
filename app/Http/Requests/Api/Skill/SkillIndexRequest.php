<?php

namespace App\Http\Requests\Api\Skill;

use Illuminate\Foundation\Http\FormRequest;

class SkillIndexRequest extends FormRequest
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
            // Search parameters
            'search' => 'sometimes|string|min:2|max:255',
            
            // Date range parameters
            'created_at' => 'sometimes|date',
            'created_from' => 'sometimes|date',
            'created_to' => 'sometimes|date|after_or_equal:created_from',
            
            // Pagination
            'per_page' => 'sometimes|integer|min:1|max:100'
        ];
    }

    /**
     * Get the validated filters for the repository.
     *
     * @return array
     */
    public function getFilters(): array
    {
        $validated = $this->validated();
        
        // Remove pagination parameters from filters
        unset($validated['per_page']);
        
        return $validated;
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
