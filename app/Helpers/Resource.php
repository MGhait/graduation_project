<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class Resource
{
    /**
     * Dynamically handle resource creation for single objects or collections.
     *
     * @param string $resourceClass The fully qualified class name of the resource (e.g., PackageResource::class).
     * @param mixed $data The data to be transformed (single object, array, or collection).
     * @return mixed Returns a resource instance or a collection of resources.
     */
    public static function make(string $resourceClass, $data)
    {
        if (!class_exists($resourceClass)) {
            throw new \InvalidArgumentException("The resource class '$resourceClass' does not exist.");
        }
        if (is_array($data) || $data instanceof Collection) {
            return $resourceClass::collection($data);
        }
        return new $resourceClass($data);
    }
}
