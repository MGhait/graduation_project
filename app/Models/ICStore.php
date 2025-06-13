<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ICStore extends Pivot
{
    protected $table = 'ic_store';
    protected $guarded = ['id'];


    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function ic()
    {
        return $this->belongsTo(IC::class, 'ic_id');
    }
}
