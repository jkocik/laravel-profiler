<?php

namespace JKocik\Laravel\Profiler\Contracts;

interface ExecutionWatcher
{
    /**
     * @return void
     */
    public function watch(): void;
}
