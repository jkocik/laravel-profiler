<?php

namespace JKocik\Laravel\Profiler\Contracts;

interface RequestHandledListener
{
    /**
     * @return void
     */
    public function listen(): void;
}
