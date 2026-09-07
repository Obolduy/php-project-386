<?php

namespace App\Booking\Infrastructure;

use App\Booking\Application\ListFreeSlots;
use App\Booking\Domain\Slot;
use App\Booking\Domain\WorkingHours;
use App\Booking\Infrastructure\Resources\MeetingTypeResource;
use App\Booking\Infrastructure\Resources\Utc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingTypesController
{
    public function __construct(
        private ListFreeSlots $listFreeSlots,
        private WorkingHours $workingHours,
    ) {}

    public function list(): JsonResponse
    {
        $meetingTypes = MeetingType::query()
            ->orderBy('duration_minutes')
            ->orderBy('id')
            ->get();

        return MeetingTypeResource::collection($meetingTypes)->response();
    }

    public function slots(Request $request, int $id): JsonResponse
    {
        $meetingType = MeetingType::query()->find($id);

        if ($meetingType === null) {
            return NotFoundResponse::meetingType();
        }

        $from = $request->query('from');
        $to = $request->query('to');

        $slots = $this->listFreeSlots->inWindow(
            $meetingType->duration_minutes,
            is_string($from) ? $from : null,
            is_string($to) ? $to : null,
        );

        return response()->json([
            'timezone' => $this->workingHours->timezone(),
            'slots' => array_map(fn (Slot $slot): array => [
                'startsAt' => Utc::format($slot->startsAt),
                'endsAt' => Utc::format($slot->endsAt),
            ], $slots),
        ]);
    }
}
