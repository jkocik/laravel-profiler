<?php

namespace JKocik\Laravel\Profiler\Contracts;

interface DataTracker
{
    /**
     * @return void
     */
    public function track(): void;
}
