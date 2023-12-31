<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
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
        'password' => 'hashed',
        'created_at' => 'datetime:y-m-d H:i:s',
        'updated_at' => 'datetime:y-m-d H:i:s',
    ];

    /**
     * Return phone number with country code and no special characters
     *
     * @return string
     */
    public function sanitizedPhoneNumber() : string
    {
        $phone =  $this->phone;

        if($phone[0] != '+') {
            $phone = '+1 '.$phone;
        }
        
        $phone = Str::replace('(', '', $phone);
        $phone = Str::replace(')', '', $phone);
        $phone = Str::replace('-', '', $phone);
        $phone = Str::replace(' ', '', $phone);

        return $phone;
    }

    public function otp() : HasOne
    {
        return $this->hasOne(Otp::class)
                    ->latest()
                    ->valid()
                    ->limit(1);
    }

    public function generateNewOtp()
    {
        $otp = $this->otp()->create();
        
        return $otp;
    }
}
