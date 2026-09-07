<?php

namespace App\Booking\Domain;

use Carbon\CarbonImmutable;

class BookingWindow
{
    public function __construct(
        private string $timezone,
        private int $days,
    ) {}

    public function firstDay(CarbonImmutable $now): CarbonImmutable
    {
        return $now->setTimezone($this->timezone)->startOfDay();
    }

    public function lastDay(CarbonImmutable $now): CarbonImmutable
    {
        return $this->firstDay($now)->addDays($this->days - 1);
    }

    public function clampFirst(?CarbonImmutable $requested, CarbonImmutable $now): CarbonImmutable
    {
        $first = $this->firstDay($now);

        return $requested !== null && $requested->greaterThan($first) ? $requested : $first;
    }

    public function clampLast(?CarbonImmutable $requested, CarbonImmutable $now): CarbonImmutable
    {
        $last = $this->lastDay($now);

        return $requested !== null && $requested->lessThan($last) ? $requested : $last;
    }
}
