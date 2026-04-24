<?php

namespace App\Observers;

use App\Models\OrderItem;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "created" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function created(OrderItem $orderItem)
    {
        //
    }

    /**
     * Handle the OrderItem "updated" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
public function updated(OrderItem $item)
{

    if (!$item->isDirty('status')) {
        \Log::info('Status not changed, skipping...');
        return;
    }

    if (
        $item->status === 'cancelled' &&
        $item->getOriginal('status') !== 'cancelled'
    ) {
        if ($item->variant) {
            $item->variant->increment('quantity', $item->quantity);

            \Log::info("Stock returned for Variant ID {$item->variant_id}, Qty: {$item->quantity}");
        }
    }

    $order = $item->order;

    if (!$order) {
        \Log::warning("Order not found for item ID: {$item->id}");
        return;
    }

    if ($order->status === 'cancelled') {
        \Log::info("Order {$order->id} is cancelled, skipping...");
        return;
    }

    //  get all valid items (exclude cancelled)
    $items = $order->items()
        ->where('status', '!=', 'cancelled')
        ->get();
        

    if ($items->count() === 0) {
        \Log::info("No valid items for Order {$order->id}");
        return;
    }

    $allDispatched = $items->every(function ($i) {
        return strtolower($i->status) === 'dispatched';
    });

    if ($allDispatched) {

        if ($order->status !== 'dispatched') {

            $order->update([
                'status' => 'dispatched',
                'updated_at' => now()
            ]);

            \Log::info("Order {$order->id} updated to DISPATCHED");
        } else {
            \Log::info("Order {$order->id} already dispatched");
        }

    } else {
        \Log::info("Order {$order->id} not fully dispatched yet");
    }
}

/**
     * Handle the OrderItem "deleted" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function deleted(OrderItem $orderItem)
    {
        //
    }

    /**
     * Handle the OrderItem "restored" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function restored(OrderItem $orderItem)
    {
        //
    }

    /**
     * Handle the OrderItem "force deleted" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function forceDeleted(OrderItem $orderItem)
    {
        //
    }
}
