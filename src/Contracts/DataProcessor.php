<?php

namespace JKocik\Laravel\Profiler\Contracts;

interface DataProcessor
{
    /**
     * @return void
     */
    public function process(): void;
}
