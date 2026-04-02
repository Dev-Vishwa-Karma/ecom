<?php

namespace App\Services;

use App\Models\UserImage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class UserImageService
{
    public function storeImage($image, $userId)
    {
        $uploaded = Cloudinary::upload(
            $image->getRealPath(),
            ['folder' => 'profile_images']
        );

        return UserImage::create([
            'user_id' => $userId,
            'image' => $uploaded->getSecurePath(),
            'public_id' => $uploaded->getPublicId()
        ]);
    }

    public function updateImage(UserImage $userImage, $image)
    {
        Cloudinary::destroy($userImage->public_id);

        $uploaded = Cloudinary::upload(
            $image->getRealPath(),
            ['folder' => 'profile_images']
        );

        return $userImage->update([
            'image' => $uploaded->getSecurePath(),
            'public_id' => $uploaded->getPublicId()
        ]);
    }

    public function deleteImage(UserImage $userImage)
    {
        Cloudinary::destroy($userImage->public_id);
        return $userImage->delete();
    }

    public function deleteExistingImage($user)
    {
        $existingImage = $user->images;
        if ($existingImage) {
            Cloudinary::destroy($existingImage->public_id);
            $existingImage->delete();
        }
    }
}