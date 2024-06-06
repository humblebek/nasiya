<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'Surname' => $this->surname,
            'Name' => $this->name,
            'Passport' => $this->passport,
            'Passport_Image' => $this->file_passport,
            'Gender' => ($this->gender == '0') ? 'Male' : (($this->gender == '1') ? 'Female' : 'Unknown'),
            'Workplace' => $this->workplace,
            'phone' => $this->phone,
        ];
    }
}
