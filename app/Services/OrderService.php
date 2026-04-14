<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SellerOrder;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    /**
     * 🔥 1. BUY NOW (Single Product Order)
     */
    public function placeOrder($validated, $product)
    {
        $variant = ProductVariant::where('id', $validated['variant_id'])
            ->where('product_id', $product->id)
            ->firstOrFail();

        if ($validated['quantity'] > $variant->quantity) {
            throw new \Exception('Stock not available');
        }

        // 1. Create Order
        $order = Order::create([
            'user_id' => Auth::id(),
            'customer_name' => $validated['customer_name'],
            'address' => $validated['address'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'],
            'payment_mode' => $validated['payment_mode'],
            'payment_status' => 'pending',
            'status' => 'pending',
            'order_number' => 'ORD-' . uniqid(),
            'order_date' => now(),
        ]);

        // 2. Order Item
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'seller_id' => $product->user_id,
            'quantity' => $validated['quantity'],
            'price' => $variant->price,
            'total_price' => $validated['total_price'],
            'status' => 'pending'
        ]);

        // 3. Sync sellers
        $this->syncSellerOrders($order->id);

        // 4. Stock decrease
        $variant->decrement('quantity', $validated['quantity']);

        // 5. Remove wishlist
        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->delete();

        return $order;
    }

    /**
     * 🔥 2. CART ORDER (Multiple Products Checkout)
     */
    public function placeCartOrder(array $items, $customerData)
    {
        // 1. Create Order
        $order = Order::create([
            'user_id' => Auth::id(),
            'customer_name' => $customerData['customer_name'],
            'address' => $customerData['address'],
            'mobile' => $customerData['mobile'],
            'email' => $customerData['email'],
            'payment_mode' => $customerData['payment_mode'],
            'payment_status' => 'pending',
            'status' => 'pending',
            'order_number' => 'ORD-' . uniqid(),
            'order_date' => now(),
        ]);

        foreach ($items as $item) {

            $variant = ProductVariant::findOrFail($item['variant_id']);
            $product = Product::findOrFail($item['product_id']);

            if ($item['quantity'] > $variant->quantity) {
                throw new \Exception('Stock not available for product ID ' . $product->id);
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'seller_id' => $product->user_id,
                'quantity' => $item['quantity'],
                'price' => $variant->price,
                'total_price' => $variant->price * $item['quantity'],
                'status' => 'pending'
            ]);

            // stock decrease
            $variant->decrement('quantity', $item['quantity']);

            // wishlist cleanup
            Wishlist::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->delete();
        }

        // 2. Group seller orders
        $this->syncSellerOrders($order->id);

        return $order;
    }

    /**
     * 🔥 Seller grouping
     */
    private function syncSellerOrders($orderId)
    {
        $items = OrderItem::where('order_id', $orderId)
            ->get()
            ->groupBy('seller_id');

        foreach ($items as $sellerId => $group) {

            SellerOrder::updateOrCreate(
                [
                    'order_id' => $orderId,
                    'seller_id' => $sellerId,
                ],
                [
                    'total_amount' => $group->sum('total_price'),
                    'status' => $this->calculateSellerStatus($group),
                ]
            );
        }
    }

    /**
     * 🔥 Seller status logic
     */
    private function calculateSellerStatus($items)
    {
        $statuses = $items->pluck('status');

        if ($statuses->every(fn($s) => $s === 'pending')) {
            return 'pending';
        }

        if ($statuses->every(fn($s) => $s === 'delivered')) {
            return 'delivered';
        }

        if ($statuses->contains('dispatched')) {
            return 'processing';
        }

        if ($statuses->contains('cancelled')) {
            return 'partial';
        }

        return 'processing';
    }
}