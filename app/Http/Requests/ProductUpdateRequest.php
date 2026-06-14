<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
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
        $productId = $this->route('inventory.product');

        return [
            'company_id'   => ['required', 'integer', 'exists:companies,id'],
            'category_id'  => ['required', 'integer', 'exists:categories,id'],
            'unit_id'      => ['required', 'integer', 'exists:units,id'],
            'product_name' => ['required', 'string', 'max:255'],
            'product_code' => ['nullable','string','max:255',Rule::unique('products', 'product_code')->ignore($productId),],
            'description'  => ['required', 'string'],
            'has_variant'  => ['required', 'boolean'],
            'is_active'    => ['required', 'boolean'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // Tambahkan ini
            'remove_image' => ['nullable', 'boolean'], // Tambahkan ini untuk opsi hapus gambar
        ];
    }
}