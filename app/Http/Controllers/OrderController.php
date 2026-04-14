<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * 🟢 BUY NOW PAGE
     */
    public function showBuyForm(Product $product, Request $request)
    {
        $variantId = $request->query('variant_id', $product->variants->first()->id);
        $quantity  = $request->query('quantity', 1);

        return view('buy-now', compact('product', 'variantId', 'quantity'));
    }

    /**
     * 🟢 BUY NOW ORDER PLACE (Single Product)
     */
    public function placeOrder(PlaceOrderRequest $request, Product $product)
    {
        try {

            $order = $this->orderService->placeOrder(
                $request->validated(),
                $product
            );

            // 🔥 Stripe redirect (if online)
            if ($request->payment_mode === 'online') {
                return redirect()->route('payment.checkout', $order->id);
            }

            // 🔥 API response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully!',
                    'data' => new OrderResource($order)
                ]);
            }

            return redirect()
                ->route('orders')
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {

            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 🟡 CART CHECKOUT PAGE
     */
  public function showCartCheckout()
{
    $items = session('cart_checkout', []);

    if (empty($items)) {
        return redirect()->route('cart')
            ->with('error', 'Cart is empty');
    }

    foreach ($items as &$item) {

        $variant = \App\Models\ProductVariant::with('product')
            ->find($item['variant_id']);

        $item['variant'] = $variant;
        $item['product'] = $variant?->product;
    }

    $user = auth()->user();

    return view('cart-checkout', compact('items', 'user'));
}

    /**
     * 🟡 CART ORDER PLACE (Multi Product)
     */
    public function placeCartOrder(Request $request)
    {
        try {

            $items = session('cart_checkout', []);

            if (empty($items)) {
                return back()->withErrors([
                    'error' => 'Cart is empty'
                ]);
            }

            $customerData = $request->only([
                'customer_name',
                'address',
                'mobile',
                'email',
                'payment_mode'
            ]);

            $order = $this->orderService->placeCartOrder(
                $items,
                $customerData
            );

            // clear cart session
            session()->forget('cart_checkout');

            // Stripe redirect
            if ($request->payment_mode === 'online') {
                return redirect()->route('payment.checkout', $order->id);
            }

            return redirect()
                ->route('orders')
                ->with('success', 'Cart order placed successfully!');

        } catch (\Exception $e) {

            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }
}