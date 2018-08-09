<?php

namespace JKocik\Laravel\Profiler\Contracts;

interface Timer
{
    /**
     * @param string $name
     * @return void
     */
    public function start(string $name): void;

    /**
     * @return void
     */
    public function startLaravel(): void;

    /**
     * @param string $name
     * @return void
     */
    public function finish(string $name): void;

    /**
     * @return void
     */
    public function finishLaravel(): void;

    /**
     * @param string $name
     * @return float
     */
    public function milliseconds(string $name): float;

    /**
     * @return array
     */
    public function all(): array;
}
