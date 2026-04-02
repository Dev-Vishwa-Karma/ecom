<?php
namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderQuantityRequest;
use App\Services\CustomerOrderService;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    protected $orderService;

    public function __construct(CustomerOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $orders = $this->orderService->getCustomerOrders($request);

        return view('orders', compact('orders'));
    }

    public function updateQuantity(UpdateOrderQuantityRequest $request, $id)
    {
        $this->orderService->updateQuantity($id, $request->validated());

        return back()->with('success','Quantity updated successfully');
    }
}