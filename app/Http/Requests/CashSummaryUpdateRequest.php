<?php

namespace App\Http\Requests;

use App\Enums\CashSummaryTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class CashSummaryUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'type'             => 'required|in:' . implode(',', CashSummaryTypeEnum::values()),
            'amount'           => 'required|integer|min:0',
            'description'      => 'nullable|string|max:1000',
            'transaction_date' => 'nullable|date',
        ];
    }

    /**
     * Get custom error messages for validation failures.
     */
    public function messages(): array
    {
        return [
            'type.required'   => 'Please select the transaction type.',
            'type.in'         => 'The selected transaction type is invalid.',
            'amount.required' => 'Please enter the amount.',
            'amount.integer'  => 'Amount must be a valid number.',
            'amount.min'      => 'Amount cannot be negative.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            $this->merge([
                'amount' => (int) preg_replace('/[^0-9]/', '', $this->amount),
            ]);
        }
    }
}