<?php

namespace App\Observers;

use App\Models\FcmToken;
use App\Models\Product;
use App\Services\FirebaseService;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function created(Product $product)
    {
        //
    }

    /**
     * Handle the Product "updated" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function updated(Product $product)
{
    $tokens = FcmToken::pluck('token')->toArray();

    \Log::info('FCM Tokens', $tokens);

    if (!empty($tokens)) {

        $firebase = new FirebaseService();

        $firebase->sendNotification(
            $tokens,
            'Product Updated',
            $product->name . ' has been updated!',
            'Updated By : '. $product->user->name

        );
    }
}

    /**
     * Handle the Product "deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function restored(Product $product)
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function forceDeleted(Product $product)
    {
        //
    }
}
