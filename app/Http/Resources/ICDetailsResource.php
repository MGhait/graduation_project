<?php

namespace App\Http\Resources;

use App\Helpers\Resource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ICDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Chip_image' => $this->chipImage ? url('storage/images/' . $this->chipImage->url) : null,
            'Logic_DiagramImage' => $this->logicDiagram ? url('storage/images/' . $this->logicDiagram->url) : null,
            'Parameters' => $this->parameters ? Resource::make(ParametersResource::class,$this->parameters): null,
            'Packages' => $this->packages ? Resource::make(PackagesResource::class,$this->packages) : null,
            'Features' => $this->features ? Resource::make(FeatureResource::class,$this->features): null,
            'Description' => $this->description,
        ];
    }
}

