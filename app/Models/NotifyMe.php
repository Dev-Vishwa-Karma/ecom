<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifyMe extends Model
{
    use HasFactory;

    protected $table = 'notify_me'; // IMPORTANT

    protected $fillable = [
        'seller_id',
        'user_id',
        'variant_id',
        'notified_at',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
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