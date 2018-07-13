<?php

namespace JKocik\Laravel\Profiler\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class LogService
{
    /**
     * @param Exception $e
     * @param bool $shouldBeLogged
     * @return void
     */
    public function error(Exception $e, bool $shouldBeLogged = true): void
    {
        if (! $shouldBeLogged) {
            return;
        }

        Log::error($e);
    }
}
