<?php

const ADMIN_MEETING_TYPES_PATH = '/api/admin/meeting-types';

it('creates a meeting type', function () {
    $response = $this->postJson(ADMIN_MEETING_TYPES_PATH, [
        'title' => 'Быстрый созвон',
        'description' => 'Короткий разговор, чтобы познакомиться.',
        'durationMinutes' => 15,
    ]);

    expectMatchesContract($response, ADMIN_MEETING_TYPES_PATH, 'post');

    $response->assertCreated()
        ->assertJsonPath('title', 'Быстрый созвон')
        ->assertJsonPath('durationMinutes', 15);

    $this->assertDatabaseCount('meeting_types', 1);
});

it('shows a created meeting type to guests immediately', function () {
    $this->postJson(ADMIN_MEETING_TYPES_PATH, [
        'title' => 'Быстрый созвон',
        'description' => 'Короткий разговор, чтобы познакомиться.',
        'durationMinutes' => 15,
    ])->assertCreated();

    $this->getJson('/api/meeting-types')
        ->assertOk()
        ->assertJsonPath('0.title', 'Быстрый созвон');
});

it('rejects a meeting type without a description', function () {
    $response = $this->postJson(ADMIN_MEETING_TYPES_PATH, [
        'title' => 'Быстрый созвон',
        'durationMinutes' => 15,
    ]);

    expectMatchesContract($response, ADMIN_MEETING_TYPES_PATH, 'post');

    $response->assertUnprocessable()
        ->assertJsonPath('code', 'validation_failed')
        ->assertJsonStructure(['fields' => ['description']]);
});

it('rejects a meeting type with a non-positive duration', function () {
    $response = $this->postJson(ADMIN_MEETING_TYPES_PATH, [
        'title' => 'Быстрый созвон',
        'description' => 'Короткий разговор.',
        'durationMinutes' => 0,
    ]);

    expectMatchesContract($response, ADMIN_MEETING_TYPES_PATH, 'post');

    $response->assertUnprocessable()->assertJsonStructure(['fields' => ['durationMinutes']]);
});
