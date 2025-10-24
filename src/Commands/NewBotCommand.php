<?php

namespace sakoora0x\Telegram\Commands;

use Illuminate\Console\Command;
use sakoora0x\Telegram\API;
use sakoora0x\Telegram\Facades\Telegram;
use sakoora0x\Telegram\Models\TelegramBot;

class NewBotCommand extends Command
{
    protected $signature = 'telegram:new-bot';

    protected $description = 'Register telegram bot in system';

    public function handle(): void
    {
        $this->start();
    }

    protected function start(): void
    {
        $token = $this->ask('Please enter telegram bot token');

        try {
            $bot = Telegram::newBot($token);

            $this->info("Telegram Bot @{$bot->username} successfully added!");
        }
        catch(\Exception $e) {
            $this->error($e->getMessage());

            $this->start();
        }
    }
}
