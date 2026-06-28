<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuItemService
{
    /**
     * Create a new menu item.
     */
    public function store(array $data)
    {
        $user = auth()->user();
        $data['company_id'] = ($user->group_id == 1) ? 1 : $user->company_id;

        // Jika ada image, upload
        if (isset($data['image']) && $data['image']) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        // Set stock kolom menjadi null (karena kita tidak pakai lagi)
        $data['stock'] = null;

        // Buat menu item
        $menuItem = MenuItem::create($data);

        // Set status awal berdasarkan stok variant yang dipilih
        $this->syncStatusFromVariant($menuItem);

        return $menuItem;
    }

    /**
     * Update an existing menu item.
     */
    public function update(MenuItem $menuItem, array $data)
    {
        // Handle image upload
        if (isset($data['image']) && $data['image']) {
            if ($menuItem->image && Storage::disk('public')->exists($menuItem->image)) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $data['image'] = $this->uploadImage($data['image']);
        }

        // Pastikan kolom stock tidak diubah dari input
        unset($data['stock']);

        $menuItem->update($data);

        // Update status berdasarkan variant baru
        $this->syncStatusFromVariant($menuItem);

        return $menuItem;
    }

    /**
     * Delete a menu item.
     */
    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->image && Storage::disk('public')->exists($menuItem->image)) {
            Storage::disk('public')->delete($menuItem->image);
        }
        return $menuItem->delete();
    }

    /**
     * Upload image (base64 or UploadedFile).
     */
    private function uploadImage($image)
    {
        if (!$image) return null;

        if (is_string($image) && str_starts_with($image, 'data:image')) {
            $imageData = explode(',', $image);
            $ext = explode('/', explode(';', $imageData[0])[0])[1] ?? 'png';
            $filename = Str::random(40) . '.' . $ext;
            $path = 'menu/' . $filename;
            Storage::disk('public')->put($path, base64_decode($imageData[1]));
            return $path;
        }

        if ($image instanceof \Illuminate\Http\UploadedFile) {
            return $image->store('menu', 'public');
        }

        return null;
    }

    /**
     * Set menu status based on the linked variant's current stock.
     */
    private function syncStatusFromVariant(MenuItem $menuItem): void
    {
        $variant = $menuItem->productVariant;
        if (!$variant || !$variant->stock) {
            // Jika tidak ada variant, status default = 'available' atau 'out'?
            // Anda bisa set 'out' karena tidak ada stok
            $menuItem->update(['status' => 'out']);
            return;
        }

        $currentStock = $variant->stock->current_stock;
        if ($currentStock <= 0) {
            $status = 'out';
        } elseif ($currentStock <= 5) {
            $status = 'low';
        } else {
            $status = 'available';
        }

        $menuItem->update(['status' => $status]);
    }
}