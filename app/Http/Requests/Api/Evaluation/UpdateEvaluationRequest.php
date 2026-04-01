<?php

namespace App\Http\Requests\Api\Evaluation;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEvaluationRequest extends FormRequest
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
        $rules =  [
            'rating'  => ['sometimes', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'evaluable_type' => ['sometimes', 'in:App\\Models\\Volunteer,App\\Models\\Task'],
        ];

        $type = $this->input('evaluable_type');
        $table = match ($type) {
            'App\\Models\\Volunteer' => 'volunteers',
            'App\\Models\\Task' => 'tasks',
            default => null,
        };
        if ($table) {
            $rules['evaluable_id'] = [
                'sometimes',
                'integer',
                Rule::exists($table, 'id')
            ];
        }
        return $rules;
    }
}
