<?php
namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderStatusRequest;
use App\Services\AdminOrderService;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    protected $orderService;

    public function __construct(AdminOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $orders = $this->orderService->getOrders($request);

        return view('admin.orders', compact('orders'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request)
    {
        $this->orderService->updateStatus($request->validated());

        return response()->json([
            'message' => 'Status updated successfully'
        ]);
    }
}