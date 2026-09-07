<?php

namespace App\Booking\Infrastructure;

use App\Booking\Application\BookingRefusal;
use App\Booking\Application\BookSlot;
use App\Booking\Infrastructure\Resources\BookingResource;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingsController
{
    public function __construct(private BookSlot $bookSlot) {}

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'meetingTypeId' => ['required', 'integer'],
            'startsAt' => ['required', 'date'],
            'guestName' => ['required', 'string', 'max:255'],
            'guestEmail' => ['required', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return ValidationResponse::from($validator);
        }

        $validated = $validator->validated();
        $meetingType = MeetingType::query()->find($validated['meetingTypeId']);

        if ($meetingType === null) {
            return NotFoundResponse::meetingType();
        }

        $result = $this->bookSlot->book(
            $meetingType,
            CarbonImmutable::parse($validated['startsAt'])->utc(),
            $validated['guestName'],
            $validated['guestEmail'],
        );

        if ($result === BookingRefusal::SlotTaken) {
            return response()->json([
                'code' => 'slot_taken',
                'message' => 'Это время уже занято. Выберите другое.',
            ], 409);
        }

        if ($result === BookingRefusal::NotABookableSlot) {
            return ValidationResponse::forField('startsAt', 'На это время записаться нельзя.');
        }

        return BookingResource::make($result)->response()->setStatusCode(201);
    }
}
