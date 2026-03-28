<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'position' => $this->position?->name,
            'salary' => $this->salary,
            'training' => $this->training,
            'cv' => $this->cv ? asset('storage/'.$this->cv) : null,
            'image' => $this->image ? asset('storage/'.$this->image) : null,
            'start_date' => $this->start_date,
        ];
    }
}
