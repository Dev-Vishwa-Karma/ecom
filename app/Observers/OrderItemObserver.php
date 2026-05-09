<?php

namespace App\Observers;

use App\Models\OrderItem;
use App\Models\OrderItemRefund;
use App\Models\Payment;
use App\Models\SellerPayout;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Refund;
use Stripe\Transfer;

class OrderItemObserver
{
    public function updated(OrderItem $item)
    {
        Log::info("Observer triggered for Item ID: {$item->id}");

        /**
         * Only proceed if status changed
         */
        if ($item->getOriginal('status') === $item->status) {
            Log::info('Status not changed, skipping...');
            return;
        }

        $order = $item->order;

        if (!$order) {
            Log::warning("Order not found for item ID: {$item->id}");
            return;
        }

        /**
         * STOCK RETURN (ONLY ON FIRST CANCEL)
         */
        if ($item->status === 'cancelled' && $item->getOriginal('status') !== 'cancelled') {

            if ($item->variant) {
                $item->variant->increment('quantity', $item->quantity);

                Log::info("Stock returned for Variant ID {$item->variant_id}, Qty: {$item->quantity}");
            }
        }

        /**
         * =========================
         *  REFUND LOGIC (IMPORTANT)
         * =========================
         */
        if ($item->status === 'cancelled' && $item->getOriginal('status') !== 'cancelled') {

            $this->processRefund($item, $order);
        }

        /**
         * ORDER STATUS UPDATE
         */
        $allItems = $order->items;

        $allCancelled = $allItems->every(fn ($i) => $i->status === 'cancelled');

        if ($allCancelled) {

            $order->update([
                'status' => 'cancelled',
                'updated_at' => now()
            ]);

            Log::info("Order {$order->id} updated to CANCELLED");
            return;
        }

        if ($order->status === 'cancelled') {
            Log::info("Order {$order->id} already cancelled");
            return;
        }

        $validItems = $allItems->where('status', '!=', 'cancelled');

        if ($validItems->count() === 0) {
            return;
        }

        $allDispatched = $validItems->every(fn ($i) => $i->status === 'dispatched');

        if ($allDispatched) {

            $order->update([
                'status' => 'dispatched',
                'updated_at' => now()
            ]);

            Log::info("Order {$order->id} updated to DISPATCHED");
        }
    }

    /**
     *  REFUND FUNCTION (CLEAN + SAFE)
     */
    
    private function processRefund(OrderItem $item, $order)
    {
        try {

            Log::info("Refund process started for Item {$item->id}");

            /**
             * Only paid orders
             */
            if ($order->payment_status !== 'paid') {
                Log::info("Refund skipped: order not paid");
                return;
            }

            $payment = Payment::where('order_id', $order->id)
                ->where('status', 'paid')
                ->first();

            if (!$payment) {
                Log::info("Refund skipped: payment not found");
                return;
            }

            /**
             * Only online payments
             */
            $allowedMethods = ['card', 'stripe', 'online'];

            if (!in_array(strtolower($payment->payment_method), $allowedMethods)) {
                Log::info("Refund skipped: offline payment");
                return;
            }

            /**
             * Prevent duplicate refund
             */
            if (isset($item->refund_status) && $item->refund_status === 'refunded') {
                Log::info("Refund already done for item {$item->id}");
                return;
            }

            $admin = User::where('role', 'super_admin')->first();
            $refundBy = $admin ? $admin->id : null;

            Stripe::setApiKey(env('STRIPE_SECRET'));

            $refundAmount = $item->total_price;

            if ($refundAmount <= 0) {
                return;
            }

            /**
             * CUSTOMER REFUND
             */
try {
    $refund = Refund::create([
        'payment_intent' => $payment->stripe_payment_intent_id,
        'amount' => (int) ($refundAmount * 100),
        'metadata' => [
            'order_id' => $order->id,
            'order_item_id' => $item->id,
            'seller_id' => $item->seller_id,
        ]
    ]);

    //  DB save
    try {
        OrderItemRefund::create([
            'order_id' => $order->id,
            'order_item_id' => $item->id,
            'seller_id' => $item->seller_id,
            'customer_id' => $order->user_id,
            'refund_by' => $refundBy, // fallback admin id
            'refund_amount' => $refundAmount,
            'stripe_refund_id' => $refund->id,
            'stripe_charge_id' => $payment->stripe_payment_intent_id,
            'status' => 'success'
        ]);
        Log::info("Refund stored in DB for Order Item {$item->id}");
    } catch (\Exception $e) {
        Log::error("Refund DB failed: ".$e->getMessage());
    }
    
    } catch (\Exception $e) {
        Log::error('Stripe Refund Failed: '.$e->getMessage());
    }         
       /**
             * SELLER REVERSE TRANSFER
             */
            $sellerPayout = SellerPayout::where('order_id', $order->id)
                ->where('seller_id', $item->seller_id)
                ->where('status', 'paid')
                ->first();

            if ($sellerPayout && $sellerPayout->stripe_transfer_id) {

                Transfer::createReversal(
                    $sellerPayout->stripe_transfer_id,
                    [
                        'amount' => (int) ($refundAmount * 100),
                        'metadata' => [
                            'order_id' => $order->id,
                            'order_item_id' => $item->id,
                        ]
                    ]
                );

                Log::info("Transfer reversed for item {$item->id}");
            }

            /**
             * MARK REFUND DONE (if columns exist)
             */
            if (isset($item->refund_status)) {
                $item->refund_status = 'refunded';
            }

            if (isset($item->refund_id)) {
                $item->refund_id = $refund->id;
            }

$item->updateQuietly([
    'refund_status' => 'refunded',
    'refund_id' => $refund->id
]);
        } catch (\Exception $e) {

            Log::error('Refund Failed', [
                'item_id' => $item->id,
                'message' => $e->getMessage()
            ]);
        }
    }
}