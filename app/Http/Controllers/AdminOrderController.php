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

        // ✅ FORCE CANCEL SERVICE
        $this->customerOrderService->cancelOrderWithReason([
            'order_id' => $request->order_id,
            'reason' => 'Cancelled by admin',
            'cancelled_by' => 'admin' // 🔥 VERY IMPORTANT
        ]);

    } else {

        // ✅ NORMAL FLOW
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