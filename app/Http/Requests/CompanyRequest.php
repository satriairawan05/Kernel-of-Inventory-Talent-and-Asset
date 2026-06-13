<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'POST' => [
                'company_name' => ['required', 'string', 'max:255'],
                'company_email' => ['required', 'string', 'email:dns', 'max:255'],
                'company_phone' => ['required', 'string', 'min:10', 'max:20'],
                'company_address' => ['required', 'string', 'max:500'],
                'company_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'bussiness_type' => ['required'],
                'use_menu' => ['required'],
                'use_service' => ['required'],
                'use_inventory' => ['required'],
            ],

            'PUT', 'PATCH' => [
                'company_name' => ['required', 'string', 'max:255'],
                'company_email' => ['required', 'string', 'email:dns', 'max:255'],
                'company_phone' => ['required', 'string', 'min:10', 'max:20'],
                'company_address' => ['required', 'string', 'max:500'],
                'company_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'bussiness_type' => ['required'],
                'use_menu' => ['required'],
                'use_service' => ['required'],
                'use_inventory' => ['required'],
            ],

            default => [],
        };
    }
}
