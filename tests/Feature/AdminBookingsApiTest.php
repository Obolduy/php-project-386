<?php

use App\Booking\Infrastructure\Booking;
use App\Booking\Infrastructure\MeetingType;
use Carbon\CarbonImmutable;

const ADMIN_BOOKINGS_PATH = '/api/admin/bookings';

beforeEach(function () {
    config(['owner.timezone' => 'Europe/Moscow']);
});

it('lists upcoming bookings of every meeting type in one list', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));
    $short = MeetingType::factory()->lasting(15)->create();
    $long = MeetingType::factory()->lasting(60)->create();

    Booking::factory()->for($long, 'meetingType')
        ->at(CarbonImmutable::parse('2026-09-16 09:00', 'UTC'), 60)->create();
    Booking::factory()->for($short, 'meetingType')
        ->at(CarbonImmutable::parse('2026-09-15 09:00', 'UTC'), 15)->create();

    $response = $this->getJson(ADMIN_BOOKINGS_PATH);

    expectMatchesContract($response, ADMIN_BOOKINGS_PATH);

    $response->assertOk()
        ->assertJsonCount(2)
        ->assertJsonPath('0.startsAt', '2026-09-15T09:00:00Z')
        ->assertJsonPath('0.meetingTypeId', $short->id)
        ->assertJsonPath('1.meetingTypeId', $long->id);
});

it('leaves past bookings out', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(30)->create();

    Booking::factory()->for($meetingType, 'meetingType')
        ->at(CarbonImmutable::parse('2026-09-10 09:00', 'UTC'), 30)->create();
    Booking::factory()->for($meetingType, 'meetingType')
        ->at(CarbonImmutable::parse('2026-09-15 09:00', 'UTC'), 30)->create();

    $response = $this->getJson(ADMIN_BOOKINGS_PATH);

    expectMatchesContract($response, ADMIN_BOOKINGS_PATH);

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.startsAt', '2026-09-15T09:00:00Z');
});

it('shows the guest behind every booking', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(30)->create();

    Booking::factory()->for($meetingType, 'meetingType')
        ->at(CarbonImmutable::parse('2026-09-15 09:00', 'UTC'), 30)
        ->create(['guest_name' => 'Владислав', 'guest_email' => 'vlad@example.com']);

    $this->getJson(ADMIN_BOOKINGS_PATH)
        ->assertOk()
        ->assertJsonPath('0.guestName', 'Владислав')
        ->assertJsonPath('0.guestEmail', 'vlad@example.com');
});

it('keeps a meeting that is happening right now', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 12:15', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(30)->create();

    Booking::factory()->for($meetingType, 'meetingType')
        ->at(CarbonImmutable::parse('2026-09-14 09:00', 'UTC'), 30)->create();

    $this->getJson(ADMIN_BOOKINGS_PATH)
        ->assertOk()
        ->assertJsonCount(1);
});

it('returns an empty list when nothing is booked', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));

    $response = $this->getJson(ADMIN_BOOKINGS_PATH);

    expectMatchesContract($response, ADMIN_BOOKINGS_PATH);

    $response->assertOk()->assertJsonCount(0);
});
