<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'current_stock'      => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_variant_id.required' => 'Varian produk wajib dipilih.',
            'product_variant_id.exists'   => 'Varian produk tidak valid.',
            'current_stock.required'      => 'Stok saat ini wajib diisi.',
            'current_stock.numeric'       => 'Stok harus berupa angka.',
            'current_stock.min'           => 'Stok tidak boleh negatif.',
            'current_stock.max'           => 'Stok melebihi batas maksimal (15 digit).',
        ];
    }
}