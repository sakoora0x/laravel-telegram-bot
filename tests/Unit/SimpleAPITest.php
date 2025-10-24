<?php

use Illuminate\Support\Facades\Http;
use sakoora0x\Telegram\API;

it('can fake HTTP responses before API instantiation', function () {
    // Fake BEFORE creating API instance
    Http::fake([
        '*' => Http::response([
            'ok' => true,
            'result' => true,
        ]),
    ]);

    $api = new API('test_token');
    $result = $api->setWebhook('https://example.com/webhook');

    expect($result)->toBeTrue();
});

it('tests baseUrl behavior', function () {
    Http::fake();

    $api = new API('my_token_123');

    try {
        $api->getMe();
    } catch (\Exception $e) {
        // Expected to fail, just checking what URL was called
    }

    Http::assertSent(function ($request) {
        dump($request->url());
        return true;
    });
});
