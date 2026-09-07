<?php

use App\Booking\Infrastructure\MeetingType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Osteel\OpenApi\Testing\ValidatorBuilder;
use Osteel\OpenApi\Testing\ValidatorInterface;

uses(RefreshDatabase::class);

function contractValidator(): ValidatorInterface
{
    return ValidatorBuilder::fromJsonFile(base_path('contract/openapi/openapi.json'))->getValidator();
}

it('lists meeting types', function () {
    MeetingType::factory()->create([
        'title' => 'Встреча на 30 минут',
        'description' => 'Быстрый разговор.',
        'duration_minutes' => 30,
    ]);

    $this->getJson('/api/meeting-types')
        ->assertOk()
        ->assertJsonPath('0.title', 'Встреча на 30 минут')
        ->assertJsonPath('0.durationMinutes', 30);
});

it('matches the openapi contract', function () {
    MeetingType::factory()->create();

    $response = $this->getJson('/api/meeting-types');

    expect(contractValidator()->get($response->baseResponse, '/api/meeting-types'))->toBeTrue();
});
