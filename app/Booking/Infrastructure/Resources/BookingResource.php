<?php

namespace App\Booking\Infrastructure\Resources;

use App\Booking\Infrastructure\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Booking */
class BookingResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'meetingTypeId' => $this->meeting_type_id,
            'startsAt' => Utc::format($this->starts_at),
            'endsAt' => Utc::format($this->ends_at),
            'guestName' => $this->guest_name,
            'guestEmail' => $this->guest_email,
        ];
    }
}
