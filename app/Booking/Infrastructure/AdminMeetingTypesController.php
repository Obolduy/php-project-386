<?php

namespace App\Booking\Infrastructure;

use App\Booking\Infrastructure\Resources\MeetingTypeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminMeetingTypesController
{
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'durationMinutes' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return ValidationResponse::from($validator);
        }

        $validated = $validator->validated();

        $meetingType = MeetingType::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'duration_minutes' => $validated['durationMinutes'],
        ]);

        return MeetingTypeResource::make($meetingType)->response()->setStatusCode(201);
    }
}
