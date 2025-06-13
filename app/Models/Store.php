<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Store extends Model
{
    protected $table = 'stores';
    protected $guarded = ['id'];

//    public function ic() : HasMany
//    {
//        return $this->hasMany(IC::class);
//    }

    public function ics()
    {
        return $this
            ->belongsToMany(IC::class, 'ic_store', 'store_id', 'ic_id')
            ->using(ICStore::class)
            ->withPivot(['quantity', 'price'])
            ->withTimestamps();
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(ICStore::class, 'store_id');
    }

    public static function getNearby($lat, $lng, $radiusInKm = 10)
    {
        return DB::table('stores')
            ->select('*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longtitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$lat, $lng, $lat]
            )
            ->having('distance', '<', $radiusInKm)
            ->orderBy('distance')
            ->get();
    }
}
