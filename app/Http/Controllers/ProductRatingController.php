<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRatingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductRatingController extends Controller
{
    public function store(ProductRatingRequest $request)
    {
               $data = $request->validated();


    auth()->user()->ratings()->create(['product_id'   => $data['product_id'],
            'variant_id'   => $data['variant_id'] ?? null,
            'rating'       => $data['rating'],
            'comment'      => $data['comment'] ?? null,
            'post_sharing' => $data['post_sharing'] ?? null,
            'posturl'      => $data['posturl'] ?? null,]);

                return redirect()->back()->with('success', 'Rating submitted successfully!');

    }

            public function uploadImage(Request $request)
        {
            $imageData = $request->input('image_data'); // Base64 encoded image

            // Extract base64 image data
            $image = str_replace('data:image/png;base64,', '', $imageData);
            $image = str_replace(' ', '+', $image);
            $imageName = time() . '.png';

            // Store the image
            Storage::disk('public')->put('images/' . $imageName, base64_decode($image));

            // Return the public URL
            return response()->json([
                'url' => asset('storage/images/' . $imageName) // Ensure this URL is publicly accessible
            ]);
        }

}
