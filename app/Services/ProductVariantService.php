<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductVariantService
{
    /**
     * Store a newly created product variant in storage.
     *
     * @param  \App\Models\Product  $product
     * @param  array  $data
     * @param  \Illuminate\Http\UploadedFile|null  $imageFile
     * @return \App\Models\ProductVariant
     */
    public function store(Product $product, array $data, ?UploadedFile $imageFile = null): ProductVariant
    {
        return DB::transaction(function () use ($product, $data, $imageFile) {
            if ($imageFile) {
                $data['image'] = $imageFile->store('variants', 'public');
            }

            return $product->variants()->create($data);
        });
    }

    /**
     * Update the specified product variant in storage.
     *
     * @param  \App\Models\ProductVariant  $variant
     * @param  array  $data
     * @param  \Illuminate\Http\UploadedFile|null  $imageFile
     * @return \App\Models\ProductVariant
     */
    public function update(ProductVariant $variant, array $data, ?UploadedFile $imageFile = null): ProductVariant
    {
        return DB::transaction(function () use ($variant, $data, $imageFile) {
            if ($imageFile) {
                // Delete old image if exists
                if ($variant->image && Storage::disk('public')->exists($variant->image)) {
                    Storage::disk('public')->delete($variant->image);
                }

                // Store new image
                $data['image'] = $imageFile->store('variants', 'public');
            }

            $variant->update($data);

            return $variant;
        });
    }

    /**
     * Remove the specified product variant from storage.
     *
     * @param  \App\Models\ProductVariant  $variant
     * @return bool
     *
     * @throws \Exception
     */
    public function destroy(ProductVariant $variant): bool
    {
        return DB::transaction(function () use ($variant) {
            // Delete image if exists
            if ($variant->image && Storage::disk('public')->exists($variant->image)) {
                Storage::disk('public')->delete($variant->image);
            }

            return $variant->delete();
        });
    }
}