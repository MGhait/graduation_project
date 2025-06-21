<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class IC extends Model
{
    protected $table = 'ics';
    protected $guarded = ['id'];

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['mainImage', 'blogDiagram']);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
//    public function store() : BelongsTo
//    {
//        return $this->belongsTo(Store::class);
//    }
    public function stores()
    {
        return $this
            ->belongsToMany(Store::class,
                'ic_store', 'ic_id', 'store_id')
            ->using(ICStore::class)                // custom pivot model
            ->withPivot(['quantity', 'price'])
            ->withTimestamps();
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(ICStore::class, 'ic_id');
    }
    public function truthTables() : HasMany
    {
        return $this->hasMany(TruthTable::class, 'ic_id', 'id');
    }

    public function icDetail() : HasOne
    {
        return $this->hasOne(ICDetails::class, 'ic_id', 'id');
    }

    public function parameters()
    {
//        return $this->hasMany(Parameter::class, 'ic_details_id')
//        ->whereIn('ic_details_id', $this->icDetail()->pluck('id'));
        return $this->hasManyThrough(
            Parameter::class,
            ICDetails::class,
            'ic_id',         // Foreign key on ic_details table
            'ic_details_id', // Foreign key on parameters table
            'id',            // Local key on ics table
            'id'             // Local key on ic_details table
        );
    }

    public function features()
    {
        return $this->hasManyThrough(
            Feature::class,
            ICDetails::class,
            'ic_id',
            'ic_details_id',
            'id',
            'id'
        );
    }

    public function packages()
    {
        return $this->hasManyThrough(
            Package::class,
            ICDetails::class,
            'ic_id',
            'ic_details_id',
            'id',
            'id'
        );
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'ic_user', 'ic_id', 'user_id')->withTimestamps();
    }


    public function mainImage() : BelongsTo
    {
        return $this->belongsTo(Image::class, 'image');
    }
    public function datasheet() : BelongsTo
    {
        return $this->belongsTo(File::class, 'datasheet_file_id');
    }

    public function blogDiagram(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'blog_diagram');
    }

    public function file() : BelongsTo
    {
        return $this->belongsTo(File::class, 'datasheet_file_id', 'id');
    }
    public static function extractICCodes(string $input): array
    {
        // Regular expression to capture IC codes
//        preg_match_all('/74[A-Z0-9]+|74[0-9]+/', $input, $matches);
        preg_match_all('/\d.*\d/', $input, $matches);
        return $matches[0] ?? [];
    }

    public static function regxSearch(string $search)
    {
        $icCodes = self::getICCodes($search);
        $pattern = implode('.*', str_split($icCodes));

        return IC::whereRaw("name REGEXP ?", [$pattern])->get();
    }

    public static function getICCodes(string $input)
    {
        preg_match_all('/\d/', $input, $matches);
        return implode('', $matches[0]);
    }
}
