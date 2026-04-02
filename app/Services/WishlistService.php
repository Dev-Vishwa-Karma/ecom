<?php

namespace App\Services;

use App\Models\Wishlist;

class WishlistService
{
    public function toggle(int $userId, int $productId): array
    {
        $wishlistItem = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();

            return [
                'status' => 'removed',
                'message' => 'Removed from Wishlist'
            ];
        }

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return [
            'status' => 'added',
            'message' => 'Added to Wishlist'
        ];
    }

    public function getUserWishlist(int $userId, ?string $search = null)
    {
        $query = Wishlist::with(['product.images', 'product.user'])
            ->where('user_id', $userId);

        if ($search) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate(8);
    }

    public function formatProductsData($wishlists): string
    {
        return json_encode(
            $wishlists->pluck('product')
                ->keyBy('id')
                ->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'price' => $p->price,
                        'description' => $p->description ?? '',
                        'images' => $p->images->pluck('image')->toArray(),
                    ];
                })->toArray()
        );
    }
}