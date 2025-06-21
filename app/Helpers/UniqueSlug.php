<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class UniqueSlug {
    public static function make(string $name, string $modelClass, string $field = 'slug'): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($modelClass::where($field, $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }
}
