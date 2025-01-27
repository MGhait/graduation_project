<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class IC extends Model
{
    protected $table = 'ics';
    protected $guarded = ['id'];

    public function store() : BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
    public function truthTables() : HasMany
    {
        return $this->hasMany(TruthTable::class, 'ic_id', 'id');
    }

    public function icDetails() : HasMany
    {
        return $this->hasMany(ICDetails::class, 'ic_id', 'id');
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
