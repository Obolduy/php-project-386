<?php

namespace App\Booking\Infrastructure;

use App\Booking\Domain\Slot;
use Carbon\CarbonImmutable;

class BookedIntervals
{
    /**
     * @return list<Slot>
     */
    public function between(CarbonImmutable $from, CarbonImmutable $to): array
    {
        return Booking::query()
            ->where('ends_at', '>', $from->utc())
            ->where('starts_at', '<', $to->utc())
            ->get()
            ->map(fn (Booking $booking): Slot => new Slot(
                CarbonImmutable::parse($booking->starts_at)->utc(),
                CarbonImmutable::parse($booking->ends_at)->utc(),
            ))
            ->all();
    }
}
