<?php

namespace App\Booking\Application;

use App\Booking\Domain\BookingWindow;
use App\Booking\Domain\Slot;
use App\Booking\Domain\SlotGenerator;
use App\Booking\Domain\WorkingHours;
use App\Booking\Infrastructure\BookedIntervals;
use Carbon\CarbonImmutable;

class ListFreeSlots
{
    public function __construct(
        private SlotGenerator $slotGenerator,
        private WorkingHours $workingHours,
        private BookingWindow $bookingWindow,
        private BookedIntervals $bookedIntervals,
    ) {}

    /**
     * @return list<Slot>
     */
    public function inWindow(int $durationMinutes, ?string $from = null, ?string $to = null): array
    {
        return $this->slots($durationMinutes, $from, $to, withBookings: true);
    }

    /**
     * Слоты рабочего расписания без учёта занятости: нужны, чтобы отличить
     * занятое время от времени, на которое записаться нельзя в принципе.
     *
     * @return list<Slot>
     */
    public function inWindowIgnoringBookings(int $durationMinutes, ?string $from = null, ?string $to = null): array
    {
        return $this->slots($durationMinutes, $from, $to, withBookings: false);
    }

    /**
     * @return list<Slot>
     */
    private function slots(int $durationMinutes, ?string $from, ?string $to, bool $withBookings): array
    {
        $now = CarbonImmutable::now($this->workingHours->timezone());

        $firstDay = $this->bookingWindow->clampFirst($this->dayOrNull($from), $now);
        $lastDay = $this->bookingWindow->clampLast($this->dayOrNull($to), $now);

        if ($firstDay->greaterThan($lastDay)) {
            return [];
        }

        return $this->slotGenerator->generate(
            $this->workingHours,
            $durationMinutes,
            $firstDay,
            $lastDay,
            $now,
            $withBookings ? $this->bookedIntervals->between($firstDay, $lastDay->addDay()) : [],
        );
    }

    private function dayOrNull(?string $day): ?CarbonImmutable
    {
        if ($day === null) {
            return null;
        }

        try {
            return CarbonImmutable::createFromFormat('Y-m-d', $day, $this->workingHours->timezone())
                ?->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
