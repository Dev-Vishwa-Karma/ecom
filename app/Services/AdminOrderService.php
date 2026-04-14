<?php
namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SellerOrder;
use Illuminate\Support\Facades\Auth;

class AdminOrderService
{
    public function getOrders($request)
    {
        $query = Order::with(['items.product', 'items.variant', 'sellerOrders'])
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
     * 🔥 Update single item status (MOST IMPORTANT)
     */
    public function updateItemStatus($data)
    {
        $item = OrderItem::findOrFail($data['order_item_id']);

        $item->update([
            'status' => $data['status']
        ]);

        // after update sync seller + order status
        $this->syncAfterUpdate($item->order_id, $item->seller_id);

        return $item;
    }

    /**
     * 🔥 Sync seller + order status
     */
    private function syncAfterUpdate($orderId, $sellerId)
    {
        // 1. Update seller order
        $sellerItems = OrderItem::where('order_id', $orderId)
            ->where('seller_id', $sellerId)
            ->get();

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
     * 🔥 Order level status (global)
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
     * 🔥 Seller status logic
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
}