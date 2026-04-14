<?php
namespace App\Http\Controllers;

use App\Http\Requests\ToggleWishlistRequest;
use App\Services\WishlistService;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(
        protected WishlistService $wishlistService
    ) {}

    /**
     * Toggle single variant/product
     */
   public function toggle(ToggleWishlistRequest $request)
{
    $userId = auth()->id();
    $productId = $request->product_id;
    $variantId = $request->variant_id; // can be null

    $result = $this->wishlistService->toggle($userId, $productId, $variantId);

    return response()->json([
        'status' => $result['status'],
        'message' => $result['message'],
        'cart_count' => \App\Models\Wishlist::where('user_id', $userId)->count()
    ]);
}

    /**
     * Bulk sync from modal (DONE button)
     */
    public function bulk(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variants' => 'array'
        ]);

        $this->wishlistService->bulkSync(
            auth()->id(),
            $request->product_id,
            $request->variants ?? []
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Wishlist updated',
            'cart_count' => \App\Models\Wishlist::where('user_id', auth()->id())->count()
        ]);
    }

    /**
     * Wishlist page
     */
    public function myWishlist(Request $request)
    {
        $wishlists = $this->wishlistService->getUserWishlist(
            auth()->id(),
            $request->search
        );

        $productsDataJson = $this->wishlistService
            ->formatProductsData($wishlists);

        return view('my-wishlist',  compact('wishlists', 'productsDataJson'));
    }
    
}