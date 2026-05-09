<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SellerOrder;
use App\Models\OrderItem;

class Order extends Model
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
        'total_amount',
        'cancelled_by_type',
        'cancelled_by_id',
        'cancelled_at'
    ];

    protected $casts = [
    'order_date'    => 'datetime',
    'dispatch_date' => 'datetime',
];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
    // In Order model (Order.php)

public function products()
{
    return $this->belongsTo(Product::class); // or the correct relationship type, if using pivot tables
}
public function items()
{
    return $this->hasMany(OrderItem::class, 'order_id', 'id');

}
 public function sellerOrders()
    {
        return $this->hasMany(SellerOrder::class);
    }
    public function cancellation()
{
    return $this->hasOne(OrderCancellation::class);
}

public function cancelledByUser()
{
    return $this->belongsTo(User::class, 'cancelled_by_id');
}
}