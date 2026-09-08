<?php

it('builds asset urls over https behind a tls terminating proxy', function () {
    $insecureAssets = rtrim(config('app.url'), '/').'/build/';
    $secureAssets = str_replace('http://', 'https://', $insecureAssets);

    $this->get('/', ['X-Forwarded-Proto' => 'https'])
        ->assertOk()
        ->assertSee($secureAssets, escape: false)
        ->assertDontSee($insecureAssets, escape: false);
});
