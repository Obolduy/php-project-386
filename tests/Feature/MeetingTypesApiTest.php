<?php

use App\Booking\Infrastructure\MeetingType;

it('lists meeting types', function () {
    MeetingType::factory()->create([
        'title' => 'Встреча на 30 минут',
        'description' => 'Быстрый разговор.',
        'duration_minutes' => 30,
    ]);

    $response = $this->getJson('/api/meeting-types');

    expectMatchesContract($response, '/api/meeting-types');

    $response->assertOk()
        ->assertJsonPath('0.title', 'Встреча на 30 минут')
        ->assertJsonPath('0.durationMinutes', 30);
});

it('orders meeting types by duration', function () {
    MeetingType::factory()->lasting(60)->create();
    MeetingType::factory()->lasting(15)->create();

    $this->getJson('/api/meeting-types')
        ->assertOk()
        ->assertJsonPath('0.durationMinutes', 15)
        ->assertJsonPath('1.durationMinutes', 60);
});
