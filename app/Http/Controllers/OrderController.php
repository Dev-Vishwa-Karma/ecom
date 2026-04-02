<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;



class OrderController extends Controller
{
    private $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function showBuyForm(Product $product, Request $request)
    {
        $variantId = $request->query('variant_id', $product->variants->first()->id);
        
        $quantity  = $request->query('quantity', 1);

        return view('buy-now', compact('product', 'variantId', 'quantity'));
    }

    public function placeOrder(PlaceOrderRequest $request, Product $product)
    {
        try {
            
            $order = $this->orderService->placeOrder($request->validated(), $product);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully!',
                    'data' => new OrderResource($order)
                ]);
            }

            return redirect()->route('orders')
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {

            return back()->withErrors(['quantity' => $e->getMessage()]);
        }
    }
}