<?php

namespace App\Booking\Domain;

use Carbon\CarbonImmutable;

class Slot
{
    public function __construct(
        public CarbonImmutable $startsAt,
        public CarbonImmutable $endsAt,
    ) {}

    public function overlaps(self $other): bool
    {
        return $this->startsAt->lessThan($other->endsAt)
            && $this->endsAt->greaterThan($other->startsAt);
    }

    public function startsAtSameMoment(CarbonImmutable $moment): bool
    {
        return $this->startsAt->equalTo($moment);
    }
}
