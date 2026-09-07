<?php

namespace Database\Factories;

use App\Booking\Infrastructure\Booking;
use App\Booking\Infrastructure\MeetingType;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Booking> */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'meeting_type_id' => MeetingType::factory(),
            'starts_at' => CarbonImmutable::now()->addDay()->setTime(9, 0),
            'ends_at' => CarbonImmutable::now()->addDay()->setTime(9, 30),
            'guest_name' => 'Гость',
            'guest_email' => 'guest@example.com',
        ];
    }

    public function at(CarbonImmutable $startsAt, int $durationMinutes): static
    {
        return $this->state(fn (): array => [
            'starts_at' => $startsAt->utc(),
            'ends_at' => $startsAt->utc()->addMinutes($durationMinutes),
        ]);
    }
}
