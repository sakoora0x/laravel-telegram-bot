<?php

namespace sakoora0x\Telegram\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \sakoora0x\Telegram\Telegram
 */
class Telegram extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \sakoora0x\Telegram\Telegram::class;
    }
}
