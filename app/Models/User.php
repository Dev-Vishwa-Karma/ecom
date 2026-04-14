<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\FcmToken;
use App\Models\FcmToken as ModelsFcmToken;
use Laravel\Sanctum\HasApiTokens;
    use Tymon\JWTAuth\Contracts\JWTSubject;

use App\Models\UserImage;
use App\Models\ProductRating;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;


    

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    
       public function images()
{
    return $this->hasOne(UserImage::class);
}

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'stripe_account_id',
        'charges_enabled',
        'payouts_enabled',
        'stripe_onboarded',
        'stripe_customer_id',
        'address',
        'mobile',
        'password',
        'role',
        'status',
        'provider',
        'provider_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }

    public function ratings()
    {
        return $this->hasMany(ProductRating::class);
    }
 
}
