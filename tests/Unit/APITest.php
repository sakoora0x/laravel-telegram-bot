<?php

use Illuminate\Support\Facades\Http;
use sakoora0x\Telegram\API;
use sakoora0x\Telegram\DTO\User;
use sakoora0x\Telegram\Tests\Helpers\TelegramMocker;

describe('API', function () {
    it('can be instantiated with a token', function () {
        $api = new API('test_token_123');
        expect($api)->toBeInstanceOf(API::class);
    });

    it('getMe returns bot information', function () {
        TelegramMocker::fakeGetMeResponse();
        $api = new API('test_token_123');

        $me = $api->getMe();

        expect($me)->toBeInstanceOf(User::class);
    });

    it('setWebhook sets webhook successfully', function () {
        TelegramMocker::fakeWebhookResponse(true);
        $api = new API('test_token_123');

        $result = $api->setWebhook('https://example.com/webhook');

        expect($result)->toBeTrue();
    });

    it('deleteWebhook deletes webhook successfully', function () {
        TelegramMocker::fakeWebhookResponse(true);
        $api = new API('test_token_123');

        $result = $api->deleteWebhook();

        expect($result)->toBeTrue();
    });

    it('getUpdates returns updates array', function () {
        TelegramMocker::fakeGetUpdatesResponse([]);
        $api = new API('test_token_123');

        $result = $api->getUpdates();

        expect($result)->toBeArray();
    });

    it('setMyCommands sets bot commands successfully', function () {
        TelegramMocker::fakeAllTelegramApis();
        $api = new API('test_token_123');

        $command = \sakoora0x\Telegram\DTO\BotCommand::make([
            'command' => 'start',
            'description' => 'Start bot'
        ]);

        $result = $api->setMyCommands($command);

        expect($result)->toBeTrue();
    });

    it('setMyName sets bot name successfully', function () {
        TelegramMocker::fakeAllTelegramApis();
        $api = new API('test_token_123');

        $result = $api->setMyName('Test Bot');

        expect($result)->toBeTrue();
    });

    it('answerCallbackQuery answers callback query successfully', function () {
        TelegramMocker::fakeAllTelegramApis();
        $api = new API('test_token_123');

        $callbackQuery = \sakoora0x\Telegram\DTO\CallbackQuery::make(['id' => 'callback_id_123']);
        $result = $api->answerCallbackQuery($callbackQuery);

        expect($result)->toBeTrue();
    });

    it('getFileLink returns file download link', function () {
        Http::fake([
            '*getFile*' => Http::response([
                'ok' => true,
                'result' => [
                    'file_id' => 'file_123',
                    'file_path' => 'photos/file.jpg',
                ],
            ]),
        ]);
        $api = new API('test_token_123');

        $document = \sakoora0x\Telegram\DTO\Document::make(['file_id' => 'file_123']);
        $link = $api->getFileLink($document);

        expect($link)->toBeString();
        expect($link)->toContain('file.jpg');
    });

    it('handles API errors', function () {
        TelegramMocker::fakeErrorResponse('Bot token is invalid', 401);
        $api = new API('test_token_123');

        $api->getMe();
    })->throws(Exception::class);
});
