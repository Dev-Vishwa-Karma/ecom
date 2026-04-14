<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MyCartController extends Controller
{
//     public function myCart()
// {
//     $userId = auth()->id();

//     $products = Product::whereHas('wishlistedBy', function ($q) use ($userId) {
//         $q->where('user_id', $userId);
//     })
//     ->with(['images', 'variants']) 
//     ->get();

//     return view('my_cart', compact('products'));
// }
public function myCart()
{
    $userId = auth()->id();

    $products = Product::whereHas('wishlistedBy', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->with([
            'images',
            'variants',
            'wishlists' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }
        ])
        ->get();

    return view('my_cart', compact('products'));
}
public function clear()
{
    Wishlist::where('user_id', auth()->id())->delete();

    return response()->json([
        'success' => true,
        'message' => 'Cart cleared'
    ]);
}

public function storeSession(Request $request)
{
    $items = $request->items;

    if (!$items || count($items) == 0) {
        return response()->json([
            'success' => false,
            'message' => 'Cart empty'
        ], 422);
    }

    Session::put('cart_checkout', $items);

    return response()->json([
        'success' => true,
        'message' => 'Cart stored'
    ]);
}
}
