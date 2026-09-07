<?php

it('responds successfully', function () {
    $this->get('/booking')->assertOk();
});
