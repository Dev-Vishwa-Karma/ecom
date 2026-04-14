<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    public function placeOrder($validated, $product)
    {

        $variant = ProductVariant::where('id', $validated['variant_id'])
            ->where('product_id', $product->id)
            ->firstOrFail();

        if ($validated['quantity'] > $variant->quantity) {
            throw new \Exception('Requested quantity exceeds available stock');
        }


        $order = Order::create([
            'product_id'     => $product->id,
            'variant_id'     => $variant->id,
            'user_id'        => Auth::id(),
            'seller_id'      => $product->user_id,
            'price'          => $variant->price,
            'quantity'       => $validated['quantity'],
            'total_price'    => $validated['total_price'],
            'customer_name'  => $validated['customer_name'],
            'address'        => $validated['address'],
            'mobile'         => $validated['mobile'],
            'email'          => $validated['email'],
            'payment_mode'   => $validated['payment_mode'],
            // 'card_number'    => $validated['payment_mode'] === 'online' ? encrypt($validated['card_number']) : null,
            // 'card_cvv'       => $validated['payment_mode'] === 'online' ? encrypt($validated['card_cvv']) : null,
            // 'card_expiry'    => $validated['payment_mode'] === 'online' ? $validated['card_expiry'] : null,
            'dispatch_date'  => now()->addDays(3),
            'status'         => 'pending',
            'order_date'     => now(),
            'payment_status' => 'pending',
            'order_number' => 'ORD-' . strtoupper(uniqid()),

        ]);

    $variant->decrement('quantity', $validated['quantity']);

    //  Remove from wishlist automatically
    Wishlist::where('user_id', Auth::id())
        ->where('product_id', $product->id)
        ->delete();

    return $order;
    }
}