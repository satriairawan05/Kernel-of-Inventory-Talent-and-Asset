<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductVariantUpdateRequest extends FormRequest
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
        $variant = $this->route('variant');

        return [
            'variant_name'   => 'required|string|max:255',
            'variant_code'   => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('product_variants', 'variant_code')->ignore($variant->id),
            ],
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price'  => 'nullable|numeric|min:0',
            'is_active'      => 'nullable|boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active') ? 1 : 0,
        ]);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'variant_name.required' => 'Nama varian wajib diisi.',
            'variant_code.unique'   => 'Kode varian sudah digunakan.',
            'image.image'           => 'File harus berupa gambar.',
            'image.mimes'           => 'Format gambar harus JPG, PNG, atau WEBP.',
            'image.max'             => 'Ukuran gambar maksimal 2MB.',
            'purchase_price.numeric'=> 'Harga beli harus berupa angka.',
            'purchase_price.min'    => 'Harga beli tidak boleh negatif.',
            'selling_price.numeric' => 'Harga jual harus berupa angka.',
            'selling_price.min'     => 'Harga jual tidak boleh negatif.',
        ];
    }
}