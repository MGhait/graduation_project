<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;


    public function generateOTP()
    {
        $this->otp = rand(100000, 999999);
        $this->otp_till = now()->addMinutes(20);
        $this->save();
    }

    public function resetOTP()
    {
        $this->otp = null;
        $this->otp_till = null;
        $this->save();
    }

    public function resetPass()
    {
        $this->otp = 'access';
        $this->otp_till = now()->addMinutes(5);
        $this->save();
    }
    protected $guarded = ['id'];


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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
