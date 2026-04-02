<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'total_price' => $this->total_price,
            'customer_name' => $this->customer_name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'status' => $this->status,
            'dispatch_date' => $this->dispatch_date,
            'order_date' => $this->order_date,
        ];    }
}
