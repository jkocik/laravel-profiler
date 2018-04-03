<?php

namespace JKocik\Laravel\Profiler\Contracts;

interface Processor
{
    /**
     * @param DataTracker $dataTracker
     * @return void
     */
    public function process(DataTracker $dataTracker): void;
}
