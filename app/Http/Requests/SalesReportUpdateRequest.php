<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SalesReportUpdateRequest extends FormRequest
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
            'company_id' => ['sometimes', 'required', 'exists:companies,id'],
            'accessories_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'service_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'pulsa_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'total_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
