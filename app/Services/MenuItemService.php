<?php

namespace App\Services;

use App\Models\MenuItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuItemService
{
    public function store(array $data)
    {
        $data['company_id'] = auth()->user()->company_id;

        // Handle image upload
        if (isset($data['image']) && $data['image']) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        return MenuItem::create($data);
    }

    public function update(MenuItem $menuItem, array $data)
    {
        // Handle image upload
        if (isset($data['image']) && $data['image']) {
            // Delete old image
            if ($menuItem->image && Storage::disk('public')->exists($menuItem->image)) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $data['image'] = $this->uploadImage($data['image']);
        }

        $menuItem->update($data);
        return $menuItem;
    }

    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->image && Storage::disk('public')->exists($menuItem->image)) {
            Storage::disk('public')->delete($menuItem->image);
        }
        return $menuItem->delete();
    }

    private function uploadImage($image)
    {
        if (!$image) return null;

        // Jika base64 (dari modal file input)
        if (is_string($image) && str_starts_with($image, 'data:image')) {
            $imageData = explode(',', $image);
            $ext = explode('/', explode(';', $imageData[0])[0])[1] ?? 'png';
            $filename = Str::random(40) . '.' . $ext;
            $path = 'menu/' . $filename;

            Storage::disk('public')->put($path, base64_decode($imageData[1]));
            return $path;
        }

        // Jika dari file upload biasa
        if ($image instanceof \Illuminate\Http\UploadedFile) {
            $path = $image->store('menu', 'public');
            return $path;
        }

        return null;
    }
}