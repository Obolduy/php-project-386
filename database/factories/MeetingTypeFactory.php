<?php

namespace Database\Factories;

use App\Booking\Infrastructure\MeetingType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MeetingType> */
class MeetingTypeFactory extends Factory
{
    protected $model = MeetingType::class;

    public function definition(): array
    {
        return [
            'title' => 'Встреча на 30 минут',
            'description' => 'Быстрый разговор по видеосвязи.',
            'duration_minutes' => 30,
        ];
    }

    public function lasting(int $minutes): static
    {
        return $this->state(fn (): array => [
            'title' => "Встреча на {$minutes} минут",
            'duration_minutes' => $minutes,
        ]);
    }
}
