<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    /**
     * HANDLE ONLINE PAYMENT (STRIPE ELEMENTS)
     */
public function process(Request $request, $productId)
{
    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

    $product = \App\Models\Product::findOrFail($productId);
    $seller = $product->user;

    // ADMIN VALIDATION (TESTING PURPOSE)
    if (!env('STRIPE_SECRET')) {
        return back()->with('error', 'Admin Stripe not configured');
    }

    
    $admin = \App\Models\User::where('role', 'admin')->first();

    if (!$admin) {
        return back()->with('error', 'Admin account not found');
    }

    // STEP 1: CREATE ORDER FIRST
    $order = \App\Models\Order::create([
        'product_id' => $product->id,
        'variant_id' => $request->variant_id,
        'user_id' => auth()->id(),
        'seller_id' => $product->user_id,
        'price' => $request->total_price,
        'quantity' => $request->quantity,
        'total_price' => $request->total_price,
        'customer_name' => $request->customer_name,
        'address' => $request->address,
        'mobile' => $request->mobile,
        'email' => $request->email,
        'payment_mode' => $request->payment_mode,
        
        'status' => 'pending',
        'order_date' => now(),
    ]);

    // STEP 2: HANDLE ONLINE PAYMENT
    if ($request->payment_mode === 'online') {

        // SELLER VALIDATION
        if (
            !$seller->stripe_account_id ||
            !$seller->charges_enabled ||
            !$seller->payouts_enabled
        ) {
            return back()->with('error', 'Seller is not ready to accept payments');
        }

        try {

            $intent = \Stripe\PaymentIntent::create([
                'amount' => (int) ($order->total_price * 100),
                'currency' => 'inr',
                'payment_method' => $request->payment_method_id,
                'confirm' => true,

                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],

                'transfer_data' => [
                    'destination' => $seller->stripe_account_id,
                ],
            ]);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($intent->status !== 'succeeded') {
            
            return back()->with('error', 'Payment failed');
        }
        $variant = \App\Models\ProductVariant::findOrFail($request->variant_id);
$variant->decrement('quantity', $request->quantity);

        // SAVE PAYMENT
        \App\Models\Payment::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'seller_id' => $order->seller_id,
            'amount' => $order->total_price,
            'status' => 'paid',
            'payment_method' => 'card',
            'stripe_payment_intent_id' => $intent->id,
                'stripe_charge_id' => $intent->latest_charge,

            'paid_at' => now(),
        ]);

        // UPDATE ORDER
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing'
        ]);
    }

    // STEP 3: COD CASE
    if ($request->payment_mode === 'cod') {

        \App\Models\Payment::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'seller_id' => $order->seller_id,
            'amount' => $order->total_price,
            'status' => 'pending',
            'payment_method' => null,
        ]);
    }

    return redirect()->route('payment.success')
        ->with('success', 'Order placed successfully!');
}


public function success()
{
    return view('order_success')->with('success', 'Payment successful!');
}

/**
 *  PAYMENT CANCEL
 */
public function cancel()
{
    return view('order_cancel')->with('error', 'Payment cancelled!');
}
}   