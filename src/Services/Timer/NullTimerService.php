<?php

namespace JKocik\Laravel\Profiler\Services\Timer;

use JKocik\Laravel\Profiler\Contracts\Timer;

class NullTimerService implements Timer
{
    /**
     * @param string $name
     * @return void
     */
    public function start(string $name): void
    {

    }

    /**
     * @return void
     */
    public function startLaravel(): void
    {

    }

    /**
     * @param string $name
     * @return void
     */
    public function finish(string $name): void
    {

    }

    /**
     * @return void
     */
    public function finishLaravel(): void
    {

    }

    /**
     * @param string $name
     * @return float
     */
    public function milliseconds(string $name): float
    {
        return 0;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return [];
    }
}
