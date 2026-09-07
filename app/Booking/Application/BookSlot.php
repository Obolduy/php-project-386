<?php

namespace App\Booking\Application;

use App\Booking\Domain\Slot;
use App\Booking\Domain\WorkingHours;
use App\Booking\Infrastructure\Booking;
use App\Booking\Infrastructure\MeetingType;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BookSlot
{
    private const LOCK_KEY = 'booking:create';

    public function __construct(
        private ListFreeSlots $listFreeSlots,
        private WorkingHours $workingHours,
    ) {}

    public function book(
        MeetingType $meetingType,
        CarbonImmutable $startsAt,
        string $guestName,
        string $guestEmail,
    ): Booking|BookingRefusal {
        try {
            return Cache::lock(self::LOCK_KEY, 10)->block(5, fn (): Booking|BookingRefusal => DB::transaction(
                fn (): Booking|BookingRefusal => $this->attempt($meetingType, $startsAt, $guestName, $guestEmail)
            ));
        } catch (LockTimeoutException) {
            return BookingRefusal::SlotTaken;
        }
    }

    private function attempt(
        MeetingType $meetingType,
        CarbonImmutable $startsAt,
        string $guestName,
        string $guestEmail,
    ): Booking|BookingRefusal {
        $day = $this->dayOf($startsAt);

        $isFree = $this->contains(
            $this->listFreeSlots->inWindow($meetingType->duration_minutes, $day, $day),
            $startsAt,
        );

        if (! $isFree) {
            return $this->refusalFor($meetingType, $startsAt);
        }

        return Booking::create([
            'meeting_type_id' => $meetingType->id,
            'starts_at' => $startsAt->utc(),
            'ends_at' => $startsAt->utc()->addMinutes($meetingType->duration_minutes),
            'guest_name' => $guestName,
            'guest_email' => $guestEmail,
        ]);
    }

    private function refusalFor(MeetingType $meetingType, CarbonImmutable $startsAt): BookingRefusal
    {
        $day = $this->dayOf($startsAt);

        $onTheGrid = $this->contains(
            $this->listFreeSlots->inWindowIgnoringBookings($meetingType->duration_minutes, $day, $day),
            $startsAt,
        );

        return $onTheGrid ? BookingRefusal::SlotTaken : BookingRefusal::NotABookableSlot;
    }

    /**
     * @param  list<Slot>  $slots
     */
    private function contains(array $slots, CarbonImmutable $moment): bool
    {
        foreach ($slots as $slot) {
            if ($slot->startsAtSameMoment($moment)) {
                return true;
            }
        }

        return false;
    }

    private function dayOf(CarbonImmutable $moment): string
    {
        return $moment->setTimezone($this->workingHours->timezone())->format('Y-m-d');
    }
}
