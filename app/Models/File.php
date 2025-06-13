<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class File extends Model
{
    protected $table = 'files';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function ic(): HasOne
    {
        return $this->hasOne(Ic::class, 'datasheet_file_id', 'id');
    }
}
