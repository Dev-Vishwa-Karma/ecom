<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SellerOrder;
use App\Models\User;
use App\Models\ProductVariant;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'seller_id',
        'quantity',
        'price',
        'total_price',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function sellerOrders()
    {
        return $this->hasMany(SellerOrder::class);
    }
       public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // Relationship to the Product
    public function product()
    {
        return $this->belongsTo(Product::class, );
    }
        public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    public function cancellation()
{
    return $this->hasOne(OrderCancellation::class, 'order_item_id');
}
    

}