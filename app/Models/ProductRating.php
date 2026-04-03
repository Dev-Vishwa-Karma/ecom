<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRating extends Model
{
    use HasFactory;
        protected $table = 'ratings';

    protected $fillable = [
        'product_id',
        'user_id',
        'variant_id',
        'rating',
        'comment',
        'post_sharing',
        'posturl'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }


}
