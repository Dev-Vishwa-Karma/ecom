<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SellerOrder;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'customer_name',
        'address',
        'mobile',
        'email',
        'payment_mode',
        'payment_status',
        'status',
        'order_number',
        'order_date',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function sellerOrders()
    {
        return $this->hasMany(SellerOrder::class);
    }
}
