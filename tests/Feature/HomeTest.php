<?php

it('responds successfully', function () {
    $this->get('/')->assertOk();
});

it('links to the booking page', function () {
    $this->get('/')->assertSee('href="'.route('booking').'"', escape: false);
});

it('shows the meeting duration from config', function () {
    config(['booking.duration_minutes' => 45]);

    $this->get('/')->assertSee('45 мин');
});
