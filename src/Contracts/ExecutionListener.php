<?php

namespace JKocik\Laravel\Profiler\Contracts;

interface ExecutionListener
{
    /**
     * @return void
     */
    public function listen(): void;
}
