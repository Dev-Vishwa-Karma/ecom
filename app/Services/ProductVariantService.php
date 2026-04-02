<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\NotifyMe;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyMeMail;

class ProductVariantService
{
    public function storeStock($validated)
    {
        $product = Product::findOrFail($validated['product_id']);
        abort_unless($product->user_id === auth()->id(), 403);

        $variant = ProductVariant::firstOrNew([
            'product_id' => $product->id,
            'color'      => $validated['color'],
            'size'       => $validated['size'],
            'gender'     => $validated['gender'],
        ]);

        $newPrice = $validated['price'];
        $newQuantity = $validated['quantity'];

        if ($variant->exists) {
            $oldPrice = $variant->price;
            $oldQuantity = $variant->quantity;
            $totalQuantity = $oldQuantity + $newQuantity;

            $weightedPrice = ($oldPrice * $oldQuantity + $newPrice * $newQuantity) / $totalQuantity;

            $variant->price = $weightedPrice;
            $variant->quantity = $totalQuantity;
            $variant->save();
        } else {
            $variant->price    = $newPrice;
            $variant->quantity = $newQuantity;
            $variant->save();
        }

        return true;
    }

    public function updateVariant($request, ProductVariant $variant)
    {
        if ($request->isMethod('put') && empty($request->all())) {
            parse_str(file_get_contents('php://input'), $putData);
            $request->merge($putData);
        }

        $validated = $request->validated();

        abort_unless($variant->product->user_id === auth()->id(), 403);

        $oldQuantity = $variant->quantity;

        $newColor  = $validated['color'];
        $newSize   = $validated['size'];
        $newGender = $validated['gender'];

        if ($newColor !== $variant->color || $newSize !== $variant->size || $newGender !== $variant->gender) {

            $newVariant = ProductVariant::create([
                'product_id' => $variant->product_id,
                'color'      => $newColor,
                'size'       => $newSize,
                'gender'     => $newGender,
                'price'      => $validated['price'],
                'quantity'   => $validated['quantity'],
            ]);

            if ($newVariant->quantity > 0) {
                $this->sendNotifyMeEmails($newVariant);
            }

        } else {

            $variant->update([
                'price'    => $validated['price'],
                'quantity' => $validated['quantity'],
            ]);

            if ($variant->quantity > 0) {
                $this->sendNotifyMeEmails($variant);
            }

            if ($oldQuantity > 0 && $variant->quantity == 0) {
                NotifyMe::where('variant_id', $variant->id)
                    ->whereNotNull('notified_at')
                    ->update(['notified_at' => null]);
            }
        }

        return true;
    }

    protected function sendNotifyMeEmails(ProductVariant $variant)
    {
        $notifyUsers = NotifyMe::where('variant_id', $variant->id)
            ->whereNull('notified_at')
            ->with('user')
            ->get();

        if ($notifyUsers->isEmpty()) return;

        $seller = $variant->product->user;

        foreach ($notifyUsers as $notify) {
            if (!$notify->user) continue;

            Mail::to($notify->user->email)
                ->send(new NotifyMeMail($variant, $seller->name, $seller->email));

            $notify->update(['notified_at' => now()]);
        }
    }

    public function getVariants(Product $product)
    {
        abort_unless($product->user_id === auth()->id(), 403);

        return $product->variants->map(function($v) {
            return [
                'id'       => $v->id,
                'color'    => $v->color,
                'size'     => $v->size,
                'gender'   => $v->gender,
                'price'    => $v->price,
                'quantity' => $v->quantity,
            ];
        })->toArray();
    }

    public function deleteVariant(ProductVariant $variant)
    {
        abort_unless($variant->product->user_id === auth()->id(), 403);

        

        $variant->delete();
        

        return $this->getVariants($variant->product);
    }
}