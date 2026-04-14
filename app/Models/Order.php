<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id','variant_id', 'user_id', 'seller_id', 'price','quantity',    'total_price',   
        'customer_name', 'address', 'mobile', 'email',
        'payment_mode', 'payment_status','order_number', 'card_number', 'card_cvv', 'card_expiry',
        'dispatch_date', 'status', 'order_date'
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
}