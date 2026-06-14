<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ShiftStoreRequest extends FormRequest
{
    /**
     * Determine whether the user is authorized
     * to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules
     * that apply to the request.
     */
    public function rules(): array
    {
        return [
            'company_id' => [
                'required',
                'integer',
                'exists:companies,id',
            ],

            'shift_name' => [
                'required',
                'string',
                'max:100',
            ],

            'shift_code' => [
                'required',
                'string',
                'max:50',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],

            'late_tolerance_minutes' => [
                'required',
                'integer',
                'min:0',
                'max:180',
            ],

            'early_leave_tolerance_minutes' => [
                'required',
                'integer',
                'min:0',
                'max:180',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'company_id.required' => 'Company is required.',
            'company_id.exists' => 'Selected company does not exist.',

            'shift_name.required' => 'Shift name is required.',

            'start_time.required' => 'Start time is required.',
            'start_time.date_format' => 'Start time format must be HH:MM.',

            'end_time.required' => 'End time is required.',
            'end_time.after' => 'End time must be later than start time.',

            'late_tolerance_minutes.required' => 'Late tolerance is required.',

            'early_leave_tolerance_minutes.required' => 'Early leave tolerance is required.',
        ];
    }
}