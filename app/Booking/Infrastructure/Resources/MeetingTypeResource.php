<?php

namespace App\Booking\Infrastructure\Resources;

use App\Booking\Infrastructure\MeetingType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin MeetingType */
class MeetingTypeResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'durationMinutes' => $this->duration_minutes,
        ];
    }
}
