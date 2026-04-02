<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\ImageUploadRequest;
use App\Models\UserImage;
use App\Services\UserImageService;
use Illuminate\Http\Request;

class UserImageController extends Controller
{
    protected $userImageService;

    public function __construct(UserImageService $userImageService)
    {
        $this->userImageService = $userImageService;
    }

    public function store(ImageUploadRequest $request)
    {
        $user = auth()->user();

        // Handle the deletion of any existing images
        $this->userImageService->deleteExistingImage($user);

        // Store the new image
        $this->userImageService->storeImage($request->file('image'), $user->id);

        return back()->with('success', 'Profile Image Updated');
    }

    public function update(ImageUploadRequest $request, UserImage $userImage)
    {
        if ($userImage->user_id !== auth()->id()) {
            abort(403);
        }

        // Update the image
        $this->userImageService->updateImage($userImage, $request->file('image'));

        return back()->with('success', 'Image Updated');
    }

    public function destroy(UserImage $userImage)
    {
        if ($userImage->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete the image
        $this->userImageService->deleteImage($userImage);

        return back()->with('success', 'Image Deleted');
    }
}