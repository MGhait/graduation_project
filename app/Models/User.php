<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;
    protected $appends = ['has_location'];

    public function getHasLocationAttribute(): bool
    {
        return !empty($this->latitude) && !empty($this->longitude);
    }

    public function getRouteKeyName(): string
    {
        return 'username';
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }


    public function getNearbyStores($userLat, $userLng, $radiusInKm = 10)
    {
        return DB::table('stores')
            ->select('*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$userLat, $userLng, $userLat]
            )
            ->having('distance', '<', $radiusInKm)
            ->orderBy('distance')
            ->get();
    }

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

    public function savedIcs()
    {
        return $this->belongsToMany(IC::class, 'ic_user', 'user_id', 'ic_id')->withTimestamps();
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

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
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
