<?php
namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class AdminOrderService
{
    public function getOrders($request)
    {
        $query = Order::with(['product', 'user', 'variant'])
            ->whereHas('product', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->latest('order_date');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->search . '%')
                    ->orWhereHas('product', function ($sub) use ($request) {
                        $sub->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return $query->paginate(10);
    }

    public function updateStatus($data)
    {
        $order = Order::findOrFail($data['order_id']);
        
        $order->update([
            'status' => $data['status']
        ]);

        return $order;
    }
}