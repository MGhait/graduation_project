<?php

namespace App\Models;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ICDetails extends Model
{
    protected $table = 'ic_details';
    protected $guarded = ['id'];

    public function ic(): BelongsTo
    {
        return $this->belongsTo(IC::class, 'ic_id', 'id');
    }

    public function chipImage(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'chip');
    }

    public function logicDiagram(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'logic_diagram');
    }

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class, 'ic_details_id', 'id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class, 'ic_details_id', 'id');
    }

    public function parameters(): HasMany
    {
        return $this->hasMany(Parameter::class, 'ic_details_id', 'id');
    }
}
