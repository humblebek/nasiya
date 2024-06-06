<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'IMEI' => $this->imei,
            'Model' => $this->model,
            'Provider' => $this->provider,
            'Account' => $this->account,
            'Status' => ($this->status == '0') ? 'Not sold' : (($this->status == '1')?'Sold':'Unknown'),
        ];
    }
}
