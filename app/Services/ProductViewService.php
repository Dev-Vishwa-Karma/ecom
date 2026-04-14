<?php

namespace App\Services;

use App\Models\Product;

class ProductViewService
{
    public function getMyProducts($request)
    {
        $query = Product::with(['images', 'user', 'variants'])
            ->where('user_id', auth()->id());

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(8);

        $productsDataJson = json_encode(
            $products->keyBy('id')->map(function ($p) {
                $minPrice = $p->variants->min('price') ?? 0;
                return [
                    'id'          => $p->id,
                    'name'        => $p->name,
                    'price'       => $minPrice,
                    'min_price_formatted' => '₹' . number_format($minPrice, 2),
                    'description' => $p->description ?? '',
                    'images'      => $p->images->pluck('image')->toArray(),
                    'variants'    => $p->variants->map(function($v) {
                        return [
                            'id'       => $v->id,
                            'color'    => $v->color,
                            'size'     => $v->size,
                            'gender'   => $v->gender,
                            'price'    => $v->price,
                            'quantity' => $v->quantity,
                        ];
                    })->values()->toArray(),   
                    'has_variants'=> $p->variants->isNotEmpty(),
                ];
            })->toArray(),
            JSON_THROW_ON_ERROR
        );

        return compact('products', 'productsDataJson');
    }

    public function getAllProducts($request)
{
    $query = Product::with([
        'images',
        'user',
        'variants',
        'wishlists' => function ($q) {
            $q->where('user_id', auth()->id());
        }
    ])
    ->where('user_id', '!=', auth()->id())
    ->whereHas('user', function ($q) {
        $q->where('status', 'active');
    });

    if ($request->search) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    $products = $query->latest()->paginate(8);

    $productsDataJson = json_encode(
        $products->keyBy('id')->map(function ($p) {

            $minPrice = $p->variants->min('price') ?? 0;

            // ✅ IMPORTANT: selected variants for current user
            $selectedVariants = $p->wishlists
                ->pluck('variant_id')
                ->filter()
                ->values()
                ->toArray();

            return [
                'id' => $p->id,
                'name' => $p->name,
                'price' => $minPrice,
                'min_price_formatted' => '₹' . number_format($minPrice, 2),
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
                })->values()->toArray(),

                // ✅ THIS FIXES YOUR CHECKBOX ISSUE
                'selected_variants' => $selectedVariants,
            ];
        })->toArray()
    );

    return compact('products', 'productsDataJson');
}
}