<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
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
    public function variant()
{
    return $this->belongsTo(ProductVariant::class, 'variant_id');
}
public function images()
    {
        return $this->hasMany(ProductImage::class,'product_id','product_id');
    }
    public function ratings()
    {
        return $this->hasMany(ProductRating::class, 'variant_id');
    }
}