<?php

namespace App\Services;

use App\Models\Wishlist;

class WishlistService
{
    /**
     * Toggle single product OR variant
     */
public function toggle(int $userId, int $productId, ?int $variantId = null): array
{
    // Check if the wishlist row already exists
    $wishlistItem = \App\Models\Wishlist::where('user_id', $userId)
        ->where('product_id', $productId)
        ->where('variant_id', $variantId)
        ->first();

    if ($wishlistItem) {
        $wishlistItem->delete(); // Remove from wishlist

        return [
            'status' => 'removed',
            'message' => 'Removed from wishlist'
        ];
    }

    // Otherwise, add to wishlist
    \App\Models\Wishlist::create([
        'user_id' => $userId,
        'product_id' => $productId,
        'variant_id' => $variantId,
    ]);

    return [
        'status' => 'added',
        'message' => 'Added to wishlist'
    ];
}
    /**
     * Bulk add variants (modal Done button)
     */
    public function bulkSync(int $userId, int $productId, array $variantIds): void
    {
        // remove only this product variants
        Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();

        if (empty($variantIds)) return;

        $data = [];

        foreach ($variantIds as $variantId) {
            $data[] = [
                'user_id' => $userId,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Wishlist::insert($data);
    }

    /**
     * Get wishlist
     */
public function getUserWishlist(int $userId, ?string $search = null)
{
    $query = Wishlist::with([
        'product.images',
        'product.user',
        'product.variants' // ✅ ADD THIS
    ])
    ->where('user_id', $userId);

    if ($search) {
        $query->whereHas('product', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        });
    }

    return $query->latest()->paginate(8);
}
    /**
     * Frontend JSON
     */
public function formatProductsData($wishlists): string
{
    $data = $wishlists->groupBy('product_id')
        ->map(function ($items) {

            $p = $items->first()->product;

            // ✅ selected variants nikaalo
            $selectedVariants = $items
                ->pluck('variant_id')
                ->filter()
                ->values()
                ->toArray();

            return [
                'id' => $p->id,
                'name' => $p->name,
                'price' => $p->price,
                'description' => $p->description ?? '',
                'images' => $p->images->pluck('image')->toArray(),

                'variants' => $p->variants->map(function ($v) {
                    return [
                        'id' => $v->id,
                        'color' => $v->color,
                        'size' => $v->size,
                        'gender' => $v->gender,
                        'price' => $v->price,
                        'quantity' => $v->quantity,
                    ];
                })->toArray(),

                // ✅ IMPORTANT
                'selected_variants' => $selectedVariants,
            ];
        });

    return json_encode($data);
}

}
