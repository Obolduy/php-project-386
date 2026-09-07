<?php

namespace App\Booking\Infrastructure;

use App\Booking\Infrastructure\Resources\BookingResource;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;

class AdminBookingsController
{
    public function list(): JsonResponse
    {
        $bookings = Booking::query()
            ->where('ends_at', '>', CarbonImmutable::now()->utc())
            ->orderBy('starts_at')
            ->orderBy('id')
            ->get();

        return BookingResource::collection($bookings)->response();
    }
}
