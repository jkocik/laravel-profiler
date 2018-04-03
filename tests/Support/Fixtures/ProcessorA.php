<?php

namespace JKocik\Laravel\Profiler\Tests\Support\Fixtures;

use Illuminate\Support\Collection;
use JKocik\Laravel\Profiler\Contracts\Processor;
use JKocik\Laravel\Profiler\Contracts\DataTracker;

class ProcessorA implements Processor
{
    /**
     * @var Collection
     */
    public $meta;

    /**
     * @var Collection
     */
    public $data;

    /**
     * ProcessorA constructor.
     */
    public function __construct()
    {
        $this->meta = new Collection();
        $this->data = new Collection();
    }

    /**
     * @param DataTracker $dataTracker
     * @return void
     */
    public function process(DataTracker $dataTracker): void
    {
        $this->data = $dataTracker->data();
        $this->meta = $dataTracker->meta();
    }
}
