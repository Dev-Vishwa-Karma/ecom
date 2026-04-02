<?php

namespace App\Services;

use App\Models\ProductImage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProductImageService
{
    public function deleteImage($public_id)
    {
        $fullPublicId = 'products/' . $public_id;

        $image = ProductImage::where('public_id', $fullPublicId)->firstOrFail();

        abort_unless($image->product->user_id === auth()->id(), 403);

        if ($image->public_id) {
            Cloudinary::destroy($image->public_id);
        }

        $image->delete();

        return [
            'success' => true,
            'message' => 'Image deleted successfully'
        ];
    }
}