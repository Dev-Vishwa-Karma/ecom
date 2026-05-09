<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemRefund extends Model
{
    use HasFactory;
        protected $fillable = [
        'order_id',
        'order_item_id',
        'seller_id',
        'customer_id',
        'refund_by',
        'refund_amount',
        'stripe_refund_id',
        'stripe_charge_id',
        'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function item()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function refundedBy()
    {
        return $this->belongsTo(User::class, 'refund_by');
    }

}
