<?php
namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class CustomerOrderService
{
    public function getCustomerOrders($request)
    {
        $query = Order::with(['product', 'variant'])
            ->where('user_id', Auth::id())
            ->latest('order_date');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('product', function($sub) use ($request) {
                      $sub->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        return $query->paginate(5);
    }

    public function updateQuantity($orderId, $data)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($orderId);

        $order->quantity = $data['quantity'];
        $order->total_price = $order->price * $data['quantity'];

        $order->save();

        return $order;
    }
}