<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'user_id'      => $this->user_id,
            'service_id'   => $this->service_id,
            'booking_date' => $this->booking_date?->format('Y-m-d'),
            'booking_time' => $this->booking_time,
            'address'      => $this->address,
            'notes'        => $this->notes,
            'status'       => $this->status,
            'user'         => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ]),
            'service'      => new ServiceResource($this->whenLoaded('service')),
            'created_at'   => $this->created_at?->toISOString(),
        ];
    }
}
