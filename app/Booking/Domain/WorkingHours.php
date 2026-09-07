<?php

namespace App\Booking\Domain;

use Carbon\CarbonImmutable;

class WorkingHours
{
    public function __construct(
        private string $timezone,
        private string $start,
        private string $end,
    ) {}

    public function timezone(): string
    {
        return $this->timezone;
    }

    public function isWorkingDay(CarbonImmutable $day): bool
    {
        return ! $day->isWeekend();
    }

    public function startOf(CarbonImmutable $day): CarbonImmutable
    {
        return $this->at($day, $this->start);
    }

    public function endOf(CarbonImmutable $day): CarbonImmutable
    {
        return $this->at($day, $this->end);
    }

    private function at(CarbonImmutable $day, string $time): CarbonImmutable
    {
        [$hour, $minute] = array_map(intval(...), explode(':', $time));

        return $day->setTimezone($this->timezone)->setTime($hour, $minute);
    }
}
