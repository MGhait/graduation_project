<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parameter extends Model
{
    protected $table = 'parameters';

    protected $guarded = ['id'];

    public function icDetails() :BelongsTo
    {
        return $this->belongsTo(ICDetails::class , 'ic_detail_id','id');
    }
}
