<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProductService
{
    /**
     * Search products by name or code.
     */
    public function search(string $keyword = ''): Collection
    {
        $search = trim($keyword);

        $query = Product::query()->with(['variants']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('product_code', 'LIKE', '%' . $search . '%');
            });
        }

        return $query->orderBy('product_name')->get();
    }

    /**
     * Store a new product record.
     */
    public function store(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // Handle image upload if exists
            $imagePath = null;
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $imagePath = $this->uploadImage($data['image']);
            }
            
            return Product::create([
                'company_id'   => $data['company_id'] ?? null,
                // 'category_id'  => $data['category_id'] ?? null,
                'unit_id'      => $data['unit_id'] ?? null,
                'product_name' => $data['product_name'] ?? null,
                'product_code' => $data['product_code'] ?? null,
                'description'  => $data['description'] ?? null,
                'has_variant'  => $data['has_variant'] ?? false,
                'is_active'    => $data['is_active'] ?? true,
                'image'        => $imagePath,
            ]);
        });
    }

    /**
     * Update an existing product record.
     */
    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            // Handle image upload if exists
            $imagePath = $product->image; // Keep existing image by default
            
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                // Delete old image if exists
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                
                $imagePath = $this->uploadImage($data['image']);
            } elseif (isset($data['remove_image']) && $data['remove_image'] == true) {
                // Remove image if requested
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $imagePath = null;
            }
            
            $product->update([
                'company_id'   => $data['company_id'] ?? $product->company_id,
                // // 'category_id'  => $data['category_id'] ?? $product->category_id,
                'unit_id'      => $data['unit_id'] ?? $product->unit_id,
                'product_name' => $data['product_name'] ?? $product->product_name,
                'product_code' => $data['product_code'] ?? $product->product_code,
                'description'  => $data['description'] ?? $product->description,
                'has_variant'  => $data['has_variant'] ?? $product->has_variant,
                'is_active'    => $data['is_active'] ?? $product->is_active,
                'image'        => $imagePath,
            ]);

            return $product->fresh();
        });
    }

    /**
     * Delete a product record.
     */
    public function destroy(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            // Delete image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            
            return $product->delete();
        });
    }
    
    /**
     * Upload product image.
     * 
     * @param UploadedFile $image
     * @return string
     * @throws \InvalidArgumentException
     */
    private function uploadImage(UploadedFile $image): string
    {
        // Validate image format
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        
        $mimeType = $image->getMimeType();
        $extension = strtolower($image->getClientOriginalExtension());
        
        if (!in_array($mimeType, $allowedMimes) || !in_array($extension, $allowedExtensions)) {
            throw new \InvalidArgumentException(
                'Invalid image format. Only JPG, JPEG, PNG, and WEBP are allowed.'
            );
        }
        
        // Validate image size (max 5MB = 5120 KB)
        $maxSize = 5120; // KB
        if ($image->getSize() > $maxSize * 1024) {
            throw new \InvalidArgumentException(
                'Image size must not exceed 5MB.'
            );
        }
        
        // Store the image
        $path = $image->store('products', 'public');
        
        if (!$path) {
            throw new \RuntimeException('Failed to upload image.');
        }
        
        return $path;
    }
    
    /**
     * Validate image file.
     * 
     * @param mixed $image
     * @return bool
     */
    public function validateImage($image): bool
    {
        if (!$image instanceof UploadedFile) {
            return false;
        }
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower($image->getClientOriginalExtension());
        
        return in_array($extension, $allowedExtensions) 
            && $image->isValid()
            && $image->getSize() <= 5120 * 1024; // 5MB in bytes
    }
}