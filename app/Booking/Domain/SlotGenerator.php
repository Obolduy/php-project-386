<?php

namespace App\Booking\Domain;

use Carbon\CarbonImmutable;

class SlotGenerator
{
    /**
     * @param  list<Slot>  $busy
     * @return list<Slot>
     */
    public function generate(
        WorkingHours $workingHours,
        int $durationMinutes,
        CarbonImmutable $from,
        CarbonImmutable $to,
        CarbonImmutable $now,
        array $busy = [],
    ): array {
        $slots = [];

        for ($day = $from; $day->lessThanOrEqualTo($to); $day = $day->addDay()) {
            if (! $workingHours->isWorkingDay($day)) {
                continue;
            }

            $dayEnd = $workingHours->endOf($day);

            for (
                $start = $workingHours->startOf($day);
                $start->addMinutes($durationMinutes)->lessThanOrEqualTo($dayEnd);
                $start = $start->addMinutes($durationMinutes)
            ) {
                if ($start->lessThan($now)) {
                    continue;
                }

                $slot = new Slot($start, $start->addMinutes($durationMinutes));

                foreach ($busy as $taken) {
                    if ($slot->overlaps($taken)) {
                        continue 2;
                    }
                }

                $slots[] = $slot;
            }
        }

        return $slots;
    }
}
