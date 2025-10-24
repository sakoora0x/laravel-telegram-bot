<?php

use sakoora0x\Telegram\ChatAPI;
use sakoora0x\Telegram\Models\TelegramBot;
use sakoora0x\Telegram\Models\TelegramChat;

describe('TelegramChat Model', function () {
    beforeEach(function () {
        $this->bot = TelegramBot::create([
            'token' => 'test_token_123',
            'username' => 'test_bot',
            'get_me' => ['id' => 123],
        ]);

        $this->chat = TelegramChat::create([
            'bot_id' => $this->bot->id,
            'chat_id' => 987654321,
            'username' => 'testuser',
            'first_name' => 'Test',
            'last_name' => 'User',
            'chat_data' => ['key' => 'value'],
            'visits' => [1, 2, 3],
        ]);
    });

    it('can be created with fillable attributes', function () {
        expect($this->chat)->toBeInstanceOf(TelegramChat::class);
        expect($this->chat->chat_id)->toBe(987654321);
        expect($this->chat->username)->toBe('testuser');
        expect($this->chat->first_name)->toBe('Test');
        expect($this->chat->last_name)->toBe('User');
    });

    it('casts chat_data to json', function () {
        expect($this->chat->chat_data)->toBeArray();
        expect($this->chat->chat_data['key'])->toBe('value');
    });

    it('casts visits to collection', function () {
        expect($this->chat->visits)->toBeInstanceOf(\Illuminate\Support\Collection::class);
        expect($this->chat->visits->toArray())->toBe([1, 2, 3]);
    });

    it('casts live_period to integer', function () {
        $this->chat->update(['live_period' => '300']);

        expect($this->chat->live_period)->toBeInt();
        expect($this->chat->live_period)->toBe(300);
    });

    it('casts live_launch_at to datetime', function () {
        $this->chat->update(['live_launch_at' => now()]);

        expect($this->chat->live_launch_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    it('casts live_expire_at to datetime', function () {
        $this->chat->update(['live_expire_at' => now()->addHour()]);

        expect($this->chat->live_expire_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    it('has a bot relationship', function () {
        $relation = $this->chat->bot();

        expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
    });

    it('can access associated bot', function () {
        $bot = $this->chat->bot;

        expect($bot)->toBeInstanceOf(TelegramBot::class);
        expect($bot->id)->toBe($this->bot->id);
    });

    it('can return a ChatAPI instance', function () {
        $api = $this->chat->api();

        expect($api)->toBeInstanceOf(ChatAPI::class);
    });

    it('can be queried by chat_id', function () {
        $found = TelegramChat::where('chat_id', 987654321)->first();

        expect($found->id)->toBe($this->chat->id);
    });

    it('can be queried by username', function () {
        $found = TelegramChat::where('username', 'testuser')->first();

        expect($found->id)->toBe($this->chat->id);
    });

    it('can be queried by bot_id', function () {
        $chats = TelegramChat::where('bot_id', $this->bot->id)->get();

        expect($chats)->toHaveCount(1);
        expect($chats->first()->id)->toBe($this->chat->id);
    });

    it('stores chat_data as json in database', function () {
        $rawData = \DB::table('telegram_chats')->where('id', $this->chat->id)->first();

        expect($rawData->chat_data)->toBeString();
        expect(json_decode($rawData->chat_data, true))->toBeArray();
    });

    it('can update chat_data', function () {
        $this->chat->update(['chat_data' => ['new_key' => 'new_value']]);
        $this->chat->refresh();

        expect($this->chat->chat_data['new_key'])->toBe('new_value');
    });

    it('can update visits collection', function () {
        $this->chat->update(['visits' => [4, 5, 6]]);
        $this->chat->refresh();

        expect($this->chat->visits->toArray())->toBe([4, 5, 6]);
    });

    it('can handle null values for optional fields', function () {
        $chat = TelegramChat::create([
            'bot_id' => $this->bot->id,
            'chat_id' => 111111,
            'username' => null,
            'first_name' => 'First',
            'last_name' => null,
            'visits' => [],
        ]);

        expect($chat->username)->toBeNull();
        expect($chat->last_name)->toBeNull();
    });

    it('tracks timestamps', function () {
        expect($this->chat->created_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
        expect($this->chat->updated_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });
});
