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
            'posturl'      => $data['posturl'] ?? null,
            ]);      

                return redirect()->back()->with('success', 'Rating submitted successfully!');

    }

public function generateReviewImage(Request $request)
{
    $data = $request->all();

    try {
        if (!function_exists('imagecreatetruecolor')) {
            return response()->json(['error' => 'GD library not enabled']);
        }

        $font = public_path('fonts/DejaVuSans.ttf');
        if (!file_exists($font) || !is_readable($font)) {
            return response()->json(['error' => 'Font file missing or unreadable']);
        }

        // Final image size (FB OG recommended)
        $width = 1200;
        $height = 630;
        $image = imagecreatetruecolor($width, $height);

        // Colors
        $bg = imagecolorallocate($image, 33, 37, 41);
        $white = imagecolorallocate($image, 255, 255, 255);
        $yellow = imagecolorallocate($image, 255, 204, 0);
        $gray = imagecolorallocate($image, 200, 200, 200);
        imagefill($image, 0, 0, $bg);

        // Text helper
        $wrapTextLines = function ($text, $fontSize, $fontFile, $maxWidth) {
            $words = explode(' ', $text);
            $lines = [];
            $currentLine = '';
            foreach ($words as $word) {
                $testLine = $currentLine ? $currentLine . ' ' . $word : $word;
                $bbox = imagettfbbox($fontSize, 0, $fontFile, $testLine);
                $lineWidth = $bbox[2] - $bbox[0];
                if ($lineWidth > $maxWidth) {
                    if ($currentLine) $lines[] = $currentLine;
                    $currentLine = $word;
                } else {
                    $currentLine = $testLine;
                }
            }
            if ($currentLine) $lines[] = $currentLine;
            return $lines;
        };

        // Padding
        $padding = 50; // more padding for bigger canvas
        $y = $padding;
        $maxTextWidth = $width - 2 * $padding;

        // Product
        imagettftext($image, 36, 0, $padding, $y, $white, $font, 'Product: ' . ($data['product_name'] ?? ''));
        $y += 70;

        // Variant
        imagettftext($image, 32, 0, $padding, $y, $white, $font, 'Variant: ' . ($data['variant'] ?? ''));
        $y += 60;

        // Price
        imagettftext($image, 32, 0, $padding, $y, $white, $font, 'Price: ₹' . ($data['price'] ?? ''));
        $y += 70;

        // Rating
        $rating = (int)($data['rating'] ?? 0);
        $starString = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
        imagettftext($image, 36, 0, $padding, $y, $yellow, $font, 'Rating: ' . $starString);
        $y += 80;

        // Comment
        $comment = $data['comment'] ?? '';
        if ($comment) {
            imagettftext($image, 28, 0, $padding, $y, $gray, $font, 'Comment:');
            $y += 40;

            $lines = $wrapTextLines($comment, 28, $font, $maxTextWidth - 20);
            foreach ($lines as $line) {
                imagettftext($image, 28, 0, $padding + 20, $y, $gray, $font, $line);
                $y += 36;
            }
        }

        // Save final image
        $fileName = 'reviews/' . time() . '.png';
        $fullPath = storage_path('app/public/' . $fileName);
        if (!file_exists(dirname($fullPath))) mkdir(dirname($fullPath), 0777, true);

        imagepng($image, $fullPath);
        imagedestroy($image);

        return response()->json([
            'success' => true,
            'filename' => $fileName,
            'url' => asset('storage/' . $fileName)
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Exception occurred',
            'message' => $e->getMessage()
        ]);
    }
}
public function shareReview($filename)
{
    $fileUrl = asset('storage/reviews/' . $filename);

    // OG image ke liye page render karna
    return view('share-review', [
        'imageUrl' => $fileUrl
    ]);
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
