<?php
namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderStatusRequest;
use App\Services\AdminOrderService;
use App\Services\OrderService;
use Error;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    protected $orderService;
    protected $customerOrderService;

    public function __construct(AdminOrderService $orderService , OrderService $customerOrderService)
    {
        $this->orderService = $orderService;
        $this->customerOrderService = $customerOrderService;
    }

    public function index(Request $request)
    {
        $orders = $this->orderService->getOrders($request);

        return view('admin.orders', compact('orders'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request)
{
 if ($request->status === 'cancelled') {

        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $this->orderService->cancelSellerItems(
            $request->order_id,
            $request->reason
        );

} else {

    $this->orderService->updateOrderStatusBySeller(
        $request->order_id,
        $request->status
    );
}
    return response()->json([
        'message' => 'Order status updated'
    ]);
}
}