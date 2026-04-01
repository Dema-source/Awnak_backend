<?php

namespace App\Http\Requests\Api\VolunteerCertificate;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVolunteerCertificateRequest extends FormRequest
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
            'volunteer_id' => ['sometimes', 'integer', 'exists:volunteers,id'],
            'certificate_id' => ['sometimes', 'integer', 'exists:certificates,id'],
            'task_id' => ['sometimes', 'integer', 'exists:tasks,id'],
        ];
    }
}
