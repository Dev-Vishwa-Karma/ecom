<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\User;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'seller_id',
        'amount',
        'platform_fee',
        'seller_earnings',
        'currency',
        'payment_method',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'status',
        'paid_at',
    ];


    // Payment → Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Payment → User (Customer)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Payment → Seller (Admin)
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    
}
