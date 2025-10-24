<?php

use Illuminate\Support\Facades\Http;
use sakoora0x\Telegram\ChatAPI;
use sakoora0x\Telegram\DTO\Message;
use sakoora0x\Telegram\Enums\ChatAction;
use sakoora0x\Telegram\Tests\Helpers\TelegramMocker;

describe('ChatAPI', function () {
    it('can be instantiated with token and chat_id', function () {
        $chatApi = new ChatAPI('test_token_123', 987654321);
        expect($chatApi)->toBeInstanceOf(ChatAPI::class);
    });

    it('sends text message successfully', function () {
        TelegramMocker::fakeSendMessageResponse();
        $chatApi = new ChatAPI('test_token_123', 987654321);

        $message = Message::make(['text' => 'Hello World']);
        $result = $chatApi->send($message);

        expect($result)->toBeInstanceOf(Message::class);
    });

    it('sends typing action', function () {
        Http::fake(['*' => Http::response(['ok' => true, 'result' => true])]);
        $chatApi = new ChatAPI('test_token_123', 987654321);

        $result = $chatApi->sendChatAction(ChatAction::Typing);

        expect($result)->toBeTrue();
    });

    it('deletes a message', function () {
        Http::fake(['*' => Http::response(['ok' => true, 'result' => true])]);
        $chatApi = new ChatAPI('test_token_123', 987654321);

        $message = Message::make(['message_id' => 123, 'text' => 'To be deleted']);
        $result = $chatApi->delete($message);

        expect($result)->toBeTrue();
    });
});
