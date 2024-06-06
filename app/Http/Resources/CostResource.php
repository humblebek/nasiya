<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Http;

class CostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        return [
            "id" => $this->id,
            'cost_type' => ($this->type == '1') ? 'Taxes' : (($this->type == '2') ? 'Sold phone' : (($this->type == '3') ? 'Not backed' : 'Unknown')),
            'IMEI' => ($this->type == '2') ? $this->imei : null,
            'Model' => ($this->type == '2') ? $this->model : null,
            "Name" => $this->name,
            'Amount' => $this->amount,
            "Date" => $this->date,
        ];
    }
}
