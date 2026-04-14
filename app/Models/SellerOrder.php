<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'seller_id',
        'total_amount',
        'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }   
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }
}

