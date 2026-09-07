<?php

use App\Booking\Infrastructure\MeetingType;
use Carbon\CarbonImmutable;
use Illuminate\Testing\TestResponse;

const SLOTS_PATH = '/api/meeting-types/{id}/slots';

beforeEach(function () {
    config([
        'owner.timezone' => 'Europe/Moscow',
        'owner.working_hours.start' => '09:00',
        'owner.working_hours.end' => '18:00',
        'owner.booking_window_days' => 14,
    ]);
});

function slotsOf(MeetingType $meetingType, string $query = ''): TestResponse
{
    $response = test()->getJson("/api/meeting-types/{$meetingType->id}/slots{$query}");

    expectMatchesContract($response, SLOTS_PATH);

    return $response;
}

it('fills a working day with slots of the meeting type duration', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(30)->create();

    slotsOf($meetingType, '?from=2026-09-14&to=2026-09-14')
        ->assertOk()
        ->assertJsonPath('timezone', 'Europe/Moscow')
        ->assertJsonCount(18, 'slots')
        ->assertJsonPath('slots.0.startsAt', '2026-09-14T06:00:00Z')
        ->assertJsonPath('slots.0.endsAt', '2026-09-14T06:30:00Z')
        ->assertJsonPath('slots.17.startsAt', '2026-09-14T14:30:00Z')
        ->assertJsonPath('slots.17.endsAt', '2026-09-14T15:00:00Z');
});

it('offers no slots on weekends', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(30)->create();

    slotsOf($meetingType, '?from=2026-09-19&to=2026-09-20')
        ->assertOk()
        ->assertJsonCount(0, 'slots');
});

it('drops a slot that does not fit before the end of the working day', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(45)->create();

    slotsOf($meetingType, '?from=2026-09-14&to=2026-09-14')
        ->assertOk()
        ->assertJsonCount(12, 'slots')
        ->assertJsonPath('slots.11.startsAt', '2026-09-14T14:15:00Z')
        ->assertJsonPath('slots.11.endsAt', '2026-09-14T15:00:00Z');
});

it('includes the last day of the booking window', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-16 06:00', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(30)->create();

    slotsOf($meetingType, '?from=2026-09-29&to=2026-09-29')
        ->assertOk()
        ->assertJsonCount(18, 'slots');
});

it('offers no slots on the day after the booking window', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-16 06:00', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(30)->create();

    slotsOf($meetingType, '?from=2026-09-30&to=2026-09-30')
        ->assertOk()
        ->assertJsonCount(0, 'slots');
});

it('skips hours that have already passed today', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 12:30', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(30)->create();

    slotsOf($meetingType, '?from=2026-09-14&to=2026-09-14')
        ->assertOk()
        ->assertJsonCount(11, 'slots')
        ->assertJsonPath('slots.0.startsAt', '2026-09-14T09:30:00Z');
});

it('covers the whole booking window when no dates are given', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(30)->create();

    slotsOf($meetingType)
        ->assertOk()
        ->assertJsonPath('slots.0.startsAt', '2026-09-14T06:00:00Z')
        ->assertJsonCount(180, 'slots');
});

it('falls back to the whole window when the dates are unparsable', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(30)->create();

    slotsOf($meetingType, '?from=abc&to[]=x')
        ->assertOk()
        ->assertJsonCount(180, 'slots');
});

it('reports an unknown meeting type as not found', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));

    $response = $this->getJson('/api/meeting-types/999/slots');

    expectMatchesContract($response, SLOTS_PATH);

    $response->assertNotFound()->assertJsonPath('code', 'not_found');
});
