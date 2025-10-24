<?php

use sakoora0x\Telegram\API;
use sakoora0x\Telegram\Models\TelegramBot;
use sakoora0x\Telegram\Models\TelegramChat;

describe('TelegramBot Model', function () {
    beforeEach(function () {
        $this->bot = TelegramBot::create([
            'token' => 'test_token_123',
            'username' => 'test_bot',
            'get_me' => ['id' => 123, 'first_name' => 'Test Bot'],
        ]);
    });

    it('can be created with fillable attributes', function () {
        expect($this->bot)->toBeInstanceOf(TelegramBot::class);
        expect($this->bot->token)->toBe('test_token_123');
        expect($this->bot->username)->toBe('test_bot');
    });

    it('casts get_me to json', function () {
        expect($this->bot->get_me)->toBeArray();
        expect($this->bot->get_me['id'])->toBe(123);
        expect($this->bot->get_me['first_name'])->toBe('Test Bot');
    });

    it('can return an API instance', function () {
        $api = $this->bot->api();

        expect($api)->toBeInstanceOf(API::class);
    });

    it('has a chats relationship', function () {
        $relation = $this->bot->chats();

        expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    });

    it('can create related chats', function () {
        $chat = $this->bot->chats()->create([
            'chat_id' => 987654321,
            'username' => 'testuser',
            'first_name' => 'Test',
            'last_name' => 'User',
            'visits' => [],
        ]);

        expect($chat)->toBeInstanceOf(TelegramChat::class);
        expect($chat->bot_id)->toBe($this->bot->id);
        expect($this->bot->chats()->count())->toBe(1);
    });

    it('retrieves associated chats', function () {
        $this->bot->chats()->create([
            'chat_id' => 111111,
            'username' => 'user1',
            'first_name' => 'User',
            'last_name' => 'One',
            'visits' => [],
        ]);

        $this->bot->chats()->create([
            'chat_id' => 222222,
            'username' => 'user2',
            'first_name' => 'User',
            'last_name' => 'Two',
            'visits' => [],
        ]);

        expect($this->bot->chats()->count())->toBe(2);
    });

    it('can be queried by token', function () {
        $found = TelegramBot::where('token', 'test_token_123')->first();

        expect($found->id)->toBe($this->bot->id);
    });

    it('can be queried by username', function () {
        $found = TelegramBot::where('username', 'test_bot')->first();

        expect($found->id)->toBe($this->bot->id);
    });

    it('stores get_me as json in database', function () {
        $rawData = \DB::table('telegram_bots')->where('id', $this->bot->id)->first();

        expect($rawData->get_me)->toBeString();
        expect(json_decode($rawData->get_me, true))->toBeArray();
    });
});
