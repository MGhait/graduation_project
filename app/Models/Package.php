<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Package extends Model
{
    protected $table = 'packages';
    protected $guarded = ['id'];

    public function icDetails() :BelongsTo
    {
        return $this->belongsTo(ICDetails::class, 'ic_details_id', 'id');
    }
}
