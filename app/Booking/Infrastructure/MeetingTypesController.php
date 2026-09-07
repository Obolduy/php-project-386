<?php

namespace App\Booking\Infrastructure;

use Illuminate\Http\JsonResponse;

class MeetingTypesController
{
    public function list(): JsonResponse
    {
        $meetingTypes = MeetingType::query()
            ->orderBy('duration_minutes')
            ->get()
            ->map(fn (MeetingType $meetingType): array => [
                'id' => $meetingType->id,
                'title' => $meetingType->title,
                'description' => $meetingType->description,
                'durationMinutes' => $meetingType->duration_minutes,
            ]);

        return response()->json($meetingTypes);
    }
}
