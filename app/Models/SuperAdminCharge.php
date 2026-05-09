<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuperAdminCharge extends Model{
    protected $fillable = [
        'seller_id',
        'month',
        'year',
        'total_amount',
        'commission',
        'status',
        'paid_at'   

    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    
}