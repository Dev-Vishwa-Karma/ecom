<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SellerOrder;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     *  1. BUY NOW (Single Product Order)
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
                'total_amount' => $validated['total_price'],

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

        // // 5. Remove wishlist
        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->delete();

        return $order;
    }

    /**
     *  2. CART ORDER (Multiple Products Checkout)
     */
    public function placeCartOrder(array $items, $customerData)
    {
            $grandTotal = 0; //  important

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
                    $total = $variant->price * $item['quantity'];

        //  add into grand total
        $grandTotal += $total;


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
        $order->update([
        'total_amount' => $grandTotal
    ]);

        // 2. Group seller orders
        $this->syncSellerOrders($order->id);

        return $order;
    }

    /**
     *  Seller grouping
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
     *  Seller status logic
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

public function cancelOrderWithReason($data)
{
    DB::transaction(function () use ($data) {

        $order = Order::with('items')->findOrFail($data['order_id']);

        $cancelledBy = $data['source'] ?? 'customer';

        foreach ($order->items as $item) {

            // already cancelled
            if ($item->status === 'cancelled') {
                continue;
            }

            $item->update([
                'status' => 'cancelled',
                'cancelled_by_type' => $cancelledBy,
                'cancelled_by_id' => auth()->id(),
                'cancelled_at' => now(),
            ]);

            OrderCancellation::create([
                'order_id' => $order->id,
                'order_item_id' => $item->id,
                'user_id' => auth()->id(),
                'cancelled_by_type' => $cancelledBy,
                'reason' => $data['reason'],
                'comment' => $data['comment'] ?? null,
            ]);
        }
    });
}

public function cancelSingleItem($data)
{
    DB::transaction(function () use ($data) {

        $item = OrderItem::with('order')->findOrFail($data['item_id']);

        // already cancelled
        if ($item->status === 'cancelled') {
            return;
        }

        // dispatch/delivered protect
        if (in_array($item->status, ['dispatched', 'delivered'])) {
            throw new \Exception('Item cannot be cancelled');
        }

        $cancelledBy = $data['source'] ?? 'customer';

        $item->update([
            'status' => 'cancelled',
            'cancelled_by_type' => $cancelledBy,
            'cancelled_by_id' => auth()->id(),
            'cancelled_at' => now(),
        ]);

        OrderCancellation::create([
            'order_id' => $item->order_id,
            'order_item_id' => $item->id,
            'user_id' => auth()->id(),
            'cancelled_by_type' => $cancelledBy,
            'reason' => $data['reason'],
            'comment' => $data['comment'] ?? null,
        ]);
    });
}


    public function getAllOrdersForSuperAdmin($month = null, $year = null)
{
    $query = Order::with('items');

    // Filter by month/year if provided
    if ($month && $year) {
        $query->whereMonth('created_at', $month)
              ->whereYear('created_at', $year);
    }

    $orders = $query->orderBy('created_at', 'desc')->get();

    // Aggregate counts
    $totalOrders = $orders->count();
    $deliveredOrders = $orders->where('status', 'delivered')->count();
    $cancelledOrders = $orders->where('status', 'cancelled')->count();
    $pendingOrders = $orders->where('status', 'pending')->count();
    $processingOrders = $orders->where('status', 'processing')->count();

    return [
        'orders' => $orders,
        'total' => $totalOrders,
        'delivered' => $deliveredOrders,
        'cancelled' => $cancelledOrders,
        'pending' => $pendingOrders,
        'processing' => $processingOrders,
    ];
}

}