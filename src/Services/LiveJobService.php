<?php

namespace sakoora0x\Telegram\Services;

use Illuminate\Support\Facades\Log;
use sakoora0x\Telegram\Models\TelegramChat;

class LiveJobService
{
    protected TelegramChat $chat;

    public function run(TelegramChat $chat): void
    {
        $this->chat = $chat;

        Log::error('TEST');
    }
}
