<?php

namespace App\Http\Requests\Api\Skill;

use Illuminate\Foundation\Http\FormRequest;

class SkillWithRelationsRequest extends FormRequest
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
            // Relations to load
            'relations' => 'sometimes|array',
            'relations.*' => 'string|in:profiles,opportunities',
            
            // Search parameters (for index method)
            'search' => 'sometimes|string|min:2|max:255',
            
            // Date range parameters (for index method)
            'created_at' => 'sometimes|date',
            'created_from' => 'sometimes|date',
            'created_to' => 'sometimes|date|after_or_equal:created_from',
            
            // Pagination (for index method)
            'per_page' => 'sometimes|integer|min:1|max:100'
        ];
    }

    /**
     * Get the validated relations for eager loading.
     *
     * @return array
     */
    public function getRelations(): array
    {
        return $this->validated()['relations'] ?? [];
    }

    /**
     * Get the validated filters for the repository (for index method).
     *
     * @return array
     */
    public function getFilters(): array
    {
        $validated = $this->validated();
        
        // Remove pagination and relation parameters from filters
        unset($validated['per_page'], $validated['relations']);
        
        return $validated;
    }

    /**
     * Get the per page value (for index method).
     *
     * @return int
     */
    public function getPerPage(): int
    {
        return (int) $this->input('per_page', 15);
    }
}
