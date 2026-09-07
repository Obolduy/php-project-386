<?php

it('serves the spa shell', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('id="app"', escape: false);
});

it('serves the spa shell on client routes', function () {
    $this->get('/meeting-types/1')->assertOk();
});
