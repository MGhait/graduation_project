<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TruthTable extends Model
{
    protected $table = 'truth_table';

    protected $guarded = ['id'];

    public function ic() :BelongsTo
    {
        $this->belongsTo(IC::class);
    }
}
