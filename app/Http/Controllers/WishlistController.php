<?php

namespace App\Http\Controllers;

use App\Http\Requests\ToggleWishlistRequest;
use App\Services\WishlistService;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    protected $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    public function toggle(ToggleWishlistRequest $request)
    {
        $result = $this->wishlistService->toggle(
            auth()->id(),
            $request->product_id
        );

        return response()->json($result);
    }

    public function myWishlist(Request $request)
    {
        $wishlists = $this->wishlistService->getUserWishlist(
            auth()->id(),
            $request->search
        );

        $productsDataJson = $this->wishlistService
            ->formatProductsData($wishlists);

        return view('my-wishlist', compact('wishlists', 'productsDataJson'));
    }
}