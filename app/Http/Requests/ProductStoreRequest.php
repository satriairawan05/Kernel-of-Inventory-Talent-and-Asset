<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
            'company_id'   => ['required', 'integer', 'exists:companies,id'],
            'category_id'  => ['required', 'integer', 'exists:categories,id'],
            'unit_id'      => ['required', 'integer', 'exists:units,id'],
            'product_name' => ['required', 'string', 'max:255'],
            'product_code' => ['required', 'string', 'max:255', 'unique:products,product_code'],
            'description'  => ['required', 'string'],
            'has_variant'  => ['required', 'boolean'],
            'is_active'    => ['required', 'boolean'],
        ];
    }
}
