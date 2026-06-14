<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SalesReportStoreRequest extends FormRequest
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
            'company_id'         => ['nullable', 'integer', 'exists:companies,id'],
            'report_date'        => ['nullable', 'date'],
            'arrived_date'       => ['nullable', 'date'],
            'accessories_amount' => ['required', 'numeric', 'min:0'],
            'service_amount'     => ['required', 'numeric', 'min:0'],
            'pulsa_amount'       => ['required', 'numeric', 'min:0'],
            'total_amount'       => ['required', 'numeric', 'min:0'],
            'notes'              => ['nullable', 'string', 'max:1000'],
        ];
    }
}
