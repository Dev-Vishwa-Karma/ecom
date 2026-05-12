<?php
namespace App\Services;

use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\OrderItem;
use App\Models\SellerOrder;
use Illuminate\Support\Facades\Auth;

class AdminOrderService
{
    public function getOrders($request)
    {
        $query = Order::with(['items.cancellation' , 'items.product', 'items.variant', 'sellerOrders'])
            ->whereHas('items.product', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->search . '%')
                    ->orWhere('order_number', 'like', '%' . $request->search . '%');
            });
        }

        

        return $query->paginate(10);
    }

    /**
     *  Update single item status (MOST IMPORTANT)
     */
    public function updateItemStatus($data)
    {
        $item = OrderItem::findOrFail($data['item_id']);

        // $item->update([
        //     'status' => $data['status']
        // ]);
        $item->status = $data['status'];
        $item->save(); //  
        // after update sync seller + order status

        return $item;
    }

    public function updateOrderStatusBySeller($orderId, $status)
{
    $sellerId = auth()->id();

    // 1. Update ALL items of this seller in this order
    $items = OrderItem::where('order_id', $orderId)
    ->where('seller_id', $sellerId)
    ->get();

foreach ($items as $item) {
    $item->status = $status;
    $item->save(); //  
}

    // 2. Update seller_order
    SellerOrder::where('order_id', $orderId)
        ->where('seller_id', $sellerId)
        ->update([
            'status' => $status
        ]);

    // 3. Sync main order status
    $allItems = OrderItem::where('order_id', $orderId)->get();

    
}

    /**
     *  Sync seller + order status
     */
    private function syncAfterUpdate($orderId, $sellerId)
    {
        if (!$sellerId) {
        return;
    }
        // 1. Update seller order
        $sellerItems = OrderItem::where('order_id', $orderId)
            ->where('seller_id', $sellerId)
            ->get();

             if ($sellerItems->isEmpty()) {
        return;
    }
        SellerOrder::where('order_id', $orderId)
            ->where('seller_id', $sellerId)
            ->update([
                'status' => $this->calculateSellerStatus($sellerItems)
            ]);

        // 2. Update main order status
        $allItems = OrderItem::where('order_id', $orderId)->get();

        Order::where('id', $orderId)->update([
            'status' => $this->calculateOrderStatus($allItems)
        ]);
    }

    /**
     *  Order level status (global)
     */
    private function calculateOrderStatus($items)
    {
        $statuses = $items->pluck('status');

        if ($statuses->every(fn($s) => $s === 'delivered')) {
            return 'delivered';
        }

        if ($statuses->contains('dispatched')) {
            return 'processing';
        }

        if ($statuses->contains('cancelled')) {
            return 'partially_cancelled';
        }

        return 'pending';
    }

    /**
     *  Seller status logic
     */
    private function calculateSellerStatus($items)
    {
        $statuses = $items->pluck('status');

        if ($statuses->every(fn($s) => $s === 'delivered')) {
            return 'delivered';
        }

        if ($statuses->contains('dispatched')) {
            return 'processing';
        }

        if ($statuses->contains('cancelled')) {
            return 'partial';
        }

        return 'pending';
    }
    public function cancelSellerItems($orderId , $reason)
{
    $sellerId = auth()->id();

    // ONLY seller items
    $items = OrderItem::where('order_id', $orderId)
        ->where('seller_id', $sellerId)
        ->where('status', '!=', 'cancelled')
        ->get();

    foreach ($items as $item) {

        $item->update([
            'status' => 'cancelled',
            'cancelled_by_type' => 'seller',
            'cancelled_by_id' => auth()->id(),
            'cancelled_at' => now(),
        ]);
    }
    OrderCancellation::create([
    'order_id' => $item->order_id,
    'order_item_id' => $item->id,
    'user_id' => auth()->id(),
    'cancelled_by_type' => 'seller',
    'reason' => $reason,
]);

    // seller order update
    SellerOrder::where('order_id', $orderId)
        ->where('seller_id', $sellerId)
        ->update([
            'status' => 'cancelled'
        ]);
}
}