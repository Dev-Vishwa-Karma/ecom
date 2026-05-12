<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCancellation extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'user_id',
        'cancelled_by_type',
        'order_item_id',
        'reason',
        'comment'
    ];
}
