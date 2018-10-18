<?php

namespace JKocik\Laravel\Profiler\Services\Performance;

use JKocik\Laravel\Profiler\Contracts\Timer;

class NullTimerService implements Timer
{
    /**
     * @param string $name
     * @return void
     */
    public function startCustom(string $name): void
    {

    }

    /**
     * @param string $name
     * @return void
     */
    public function finishCustom(string $name): void
    {

    }

    /**
     * @param string $name
     * @return float
     */
    public function millisecondsCustom(string $name): float
    {
        return -1;
    }
}
