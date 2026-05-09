<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessDetail extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id','email','business_name','business_phone',
        'bank_account_number','ifsc_code','account_holder_name',
        'business_address',
        'pan_card','gst_certificate',
        'status','approved_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
