<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;           // for the creator
use App\Models\ProductImage;   // relation

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'user_id',       
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function wishlistedBy()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function orders()
{
    return $this->hasMany(Order::class);
}
public function wishlists()
{
    return $this->hasMany(Wishlist::class, 'product_id');
}
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id'); // Product has a seller
    }

}