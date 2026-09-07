<?php

use App\Booking\Infrastructure\MeetingType;
use Carbon\CarbonImmutable;
use Illuminate\Testing\TestResponse;

const BOOKINGS_PATH = '/api/bookings';

beforeEach(function () {
    config([
        'owner.timezone' => 'Europe/Moscow',
        'owner.working_hours.start' => '09:00',
        'owner.working_hours.end' => '18:00',
        'owner.booking_window_days' => 14,
    ]);
});

function bookAt(MeetingType $meetingType, string $startsAt, array $overrides = []): TestResponse
{
    $response = test()->postJson(BOOKINGS_PATH, array_merge([
        'meetingTypeId' => $meetingType->id,
        'startsAt' => $startsAt,
        'guestName' => 'Владислав',
        'guestEmail' => 'guest@example.com',
    ], $overrides));

    expectMatchesContract($response, BOOKINGS_PATH, 'post');

    return $response;
}

it('books a free slot', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(30)->create();

    bookAt($meetingType, '2026-09-14T09:00:00Z')
        ->assertCreated()
        ->assertJsonPath('meetingTypeId', $meetingType->id)
        ->assertJsonPath('startsAt', '2026-09-14T09:00:00Z')
        ->assertJsonPath('endsAt', '2026-09-14T09:30:00Z')
        ->assertJsonPath('guestName', 'Владислав');

    $this->assertDatabaseCount('bookings', 1);
});

it('hides a booked slot from the free slots', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(30)->create();

    bookAt($meetingType, '2026-09-14T09:00:00Z')->assertCreated();

    $this->getJson("/api/meeting-types/{$meetingType->id}/slots?from=2026-09-14&to=2026-09-14")
        ->assertOk()
        ->assertJsonCount(17, 'slots')
        ->assertJsonMissing(['startsAt' => '2026-09-14T09:00:00Z']);
});

it('refuses a slot that is already taken', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(30)->create();

    bookAt($meetingType, '2026-09-14T09:00:00Z')->assertCreated();

    bookAt($meetingType, '2026-09-14T09:00:00Z')
        ->assertConflict()
        ->assertJsonPath('code', 'slot_taken');

    $this->assertDatabaseCount('bookings', 1);
});

it('blocks overlapping slots of another meeting type', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));
    $long = MeetingType::factory()->lasting(60)->create();
    $short = MeetingType::factory()->lasting(15)->create();

    bookAt($long, '2026-09-14T09:00:00Z')->assertCreated();

    bookAt($short, '2026-09-14T09:30:00Z')
        ->assertConflict()
        ->assertJsonPath('code', 'slot_taken');

    $this->getJson("/api/meeting-types/{$short->id}/slots?from=2026-09-14&to=2026-09-14")
        ->assertOk()
        ->assertJsonMissing(['startsAt' => '2026-09-14T09:45:00Z']);
});

it('refuses a time outside the working hours', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(30)->create();

    bookAt($meetingType, '2026-09-14T01:00:00Z')
        ->assertUnprocessable()
        ->assertJsonPath('code', 'validation_failed')
        ->assertJsonStructure(['fields' => ['startsAt']]);

    $this->assertDatabaseCount('bookings', 0);
});

it('refuses a time on a weekend', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(30)->create();

    bookAt($meetingType, '2026-09-19T09:00:00Z')->assertUnprocessable();
});

it('refuses invalid guest data', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));
    $meetingType = MeetingType::factory()->lasting(30)->create();

    bookAt($meetingType, '2026-09-14T09:00:00Z', ['guestEmail' => 'не почта', 'guestName' => ''])
        ->assertUnprocessable()
        ->assertJsonStructure(['fields' => ['guestEmail', 'guestName']]);
});

it('reports an unknown meeting type as not found', function () {
    $this->travelTo(CarbonImmutable::parse('2026-09-14 06:00', 'Europe/Moscow'));

    $response = $this->postJson(BOOKINGS_PATH, [
        'meetingTypeId' => 999,
        'startsAt' => '2026-09-14T09:00:00Z',
        'guestName' => 'Владислав',
        'guestEmail' => 'guest@example.com',
    ]);

    expectMatchesContract($response, BOOKINGS_PATH, 'post');

    $response->assertNotFound()->assertJsonPath('code', 'not_found');
});
