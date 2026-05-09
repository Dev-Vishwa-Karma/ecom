<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\SellerPayout;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Transfer;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function checkout(Order $order)
    {
        $order = Order::with('items')->findOrFail($order->id);

        //  Prevent double payment (already paid)
        if ($order->payment_status === 'paid') {
            return redirect()->route('payment.success')
                ->with('success', 'Order already paid');
        }

        $totalAmount = $order->items->sum('total_price');

        if ($totalAmount <= 0) {
            return back()->with('error', 'Invalid order amount');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $intent = PaymentIntent::create([
            'amount' => (int) ($totalAmount * 100),
            'currency' => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => [
                'order_id' => $order->id,
                'user_id' => auth()->id(),
            ],
        ]);

        return view('payment-checkout', [
            'order' => $order,
            'intent' => $intent,
            'user' => auth()->user(),
        ]);
    }

    public function process(Request $request, Order $order)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $order = Order::with('items.seller')->findOrFail($order->id);
        $user = auth()->user();

        //  Prevent double payment (backend safety)
        if ($order->payment_status === 'paid') {
            return redirect()->route('payment.success')
                ->with('success', 'Order already paid');
        }

        $totalAmount = $order->items->sum('total_price');

        if ($totalAmount <= 0) {
            return back()->with('error', 'Invalid order amount');
        }

        //  Get PaymentIntent
        $intent = PaymentIntent::retrieve(
            $request->payment_intent_id,
            ['expand' => ['latest_charge']]
        );

        //  Check payment success
        if ($intent->status !== 'succeeded') {
            return back()->with('error', 'Payment not completed');
        }

        // Get charge ID safely
        $chargeId = is_string($intent->latest_charge)
            ? $intent->latest_charge
            : ($intent->latest_charge->id ?? null);

        if (!$chargeId) {
            \Log::error('Charge ID missing', ['intent' => $intent]);
            return back()->with('error', 'Payment verification failed');
        }

        DB::beginTransaction();

        try {

            //  Save payment
            Payment::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'amount' => $totalAmount,
                'status' => 'paid',
                'payment_method' => 'card',
                'stripe_payment_intent_id' => $intent->id,
                'stripe_charge_id' => $chargeId,
                'paid_at' => now(),
            ]);

            //  STOCK + WISHLIST
            foreach ($order->items as $item) {

                $variant = ProductVariant::find($item->variant_id);

                if ($variant) {
                    if ($variant->quantity < $item->quantity) {
                        throw new \Exception('Stock mismatch');
                    }

                    $variant->decrement('quantity', $item->quantity);
                }

                Wishlist::where('user_id', $user->id)
                    ->where('product_id', $item->product_id)
                    ->delete();
            }

            //  TRANSFER TO SELLERS
//  GROUP ITEMS BY SELLER
$sellerGroups = $order->items->groupBy('seller_id');

foreach ($sellerGroups as $sellerId => $items) {

    $seller = $items->first()->seller;

    if (!$seller || !$seller->stripe_account_id) {
        continue;
    }

    //  TOTAL OF THIS SELLER
    $sellerTotal = $items->sum('total_price');

    //  SINGLE TRANSFER
    $transfer = Transfer::create([
        'amount' => (int) ($sellerTotal * 100),
        'currency' => $intent->currency,
        'destination' => $seller->stripe_account_id,
        'source_transaction' => $chargeId,
        'metadata' => [
        'order_id' => $order->id,
        'user_id' => $user->id,
    ],
    ]);

    // SINGLE PAYOUT ENTRY
    SellerPayout::create([
        'order_id' => $order->id,
        'seller_id' => $sellerId,
        'amount' => $sellerTotal,
        'stripe_transfer_id' => $transfer->id,
        'status' => 'paid',
        'paid_at' => now(),
    ]);
}            //  Update order
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

            \Log::error('Payment full flow failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Payment failed or incomplete');
        }

        return redirect()->route('payment.success');
    }

    public function success()
    {
        return view('order_success')->with('success', 'Payment successful!');
    }

    public function cancel()
    {
        return view('order_cancel')->with('error', 'Payment cancelled!');
    }
}