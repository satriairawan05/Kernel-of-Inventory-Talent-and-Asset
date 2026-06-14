<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Konversi checkbox values ke boolean sebelum validasi
        $this->merge([
            'use_menu' => $this->boolean('use_menu'),
            'use_service' => $this->boolean('use_service'),
            'use_inventory' => $this->boolean('use_inventory'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $companyId = $this->route('setting.company') ? $this->route('setting.company')->id : null;
        
        return match ($this->method()) {
            'POST' => [
                'company_name' => ['required', 'string', 'max:255', Rule::unique('companies', 'company_name')],
                'company_email' => ['required', 'string', 'email:dns', 'max:255', Rule::unique('companies', 'company_email')],
                'company_phone' => ['required', 'string', 'min:10', 'max:20'],
                'company_address' => ['required', 'string', 'max:500'],
                'company_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'bussiness_type' => ['required', 'string', 'max:255'],
                'use_menu' => ['required', 'boolean'],
                'use_service' => ['required', 'boolean'],
                'use_inventory' => ['required', 'boolean'],
            ],

            'PUT', 'PATCH' => [
                'company_name' => ['required', 'string', 'max:255', Rule::unique('companies', 'company_name')->ignore($companyId)],
                'company_email' => ['required', 'string', 'email:dns', 'max:255', Rule::unique('companies', 'company_email')->ignore($companyId)],
                'company_phone' => ['required', 'string', 'min:10', 'max:20'],
                'company_address' => ['required', 'string', 'max:500'],
                'company_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'bussiness_type' => ['required', 'string', 'max:255'],
                'use_menu' => ['required', 'boolean'],
                'use_service' => ['required', 'boolean'],
                'use_inventory' => ['required', 'boolean'],
            ],
            default => [],
        };
    }
}