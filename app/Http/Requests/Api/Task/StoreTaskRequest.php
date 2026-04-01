<?php

namespace App\Http\Requests\Api\Task;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:100'],
            'volunteer_id' => ['required', 'integer', 'exists:volunteers,id'],
            // Supervisor
            'profile_id' => ['required', 'integer', 'exists:profiles,id'],
            'hours' => ['required', 'integer', 'min:1'],
        ];
    }
}
