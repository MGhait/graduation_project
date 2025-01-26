<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParametersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Technology_family' => $this->technology_family,
            'minimum_supply_voltage' => $this->min_voltage,
            'maximum_supply_voltage' => $this->max_voltage,
            'number_of_channels' =>$this->channels_number,
            'input_per_channel' =>$this->inputs_per_channel,
        ];
    }
}
