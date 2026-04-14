<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
        protected $table = 'product_variants'; // <-- This is crucial

    protected $fillable = [
        'product_id',
        'color',
        'size',
        'gender',
        'price',
        'quantity'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

public function images()
    {
        return $this->hasMany(ProductImage::class,'product_id','product_id');
    }
    public function ratings()
    {
        return $this->hasMany(ProductRating::class, 'variant_id');
    }
    public function wishlists()
{
    return $this->hasMany(Wishlist::class, 'product_id');
}

}