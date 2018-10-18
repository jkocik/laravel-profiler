<?php

namespace JKocik\Laravel\Profiler\Contracts;

interface Timer
{
    /**
     * @param string $name
     * @return void
     */
    public function startCustom(string $name): void;

    /**
     * @param string $name
     * @return void
     */
    public function finishCustom(string $name): void;

    /**
     * @param string $name
     * @return float
     */
    public function millisecondsCustom(string $name): float;
}
