<?php

namespace sakoora0x\Telegram\Middleware;

use Closure;
use sakoora0x\Telegram\TelegramRequest;

class LiveMiddleware
{
    public function handle(TelegramRequest $request, Closure $next, mixed $period, mixed $timeout = 3600)
    {
        $period = intval($period);
        $timeout = intval($timeout);

        $request->live($period, $timeout);

        return $next($request);
    }
}
