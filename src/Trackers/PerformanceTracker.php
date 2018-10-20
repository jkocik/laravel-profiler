<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\Timer;
use JKocik\Laravel\Profiler\Contracts\Memory;
use JKocik\Laravel\Profiler\LaravelListeners\PerformanceListener;

class PerformanceTracker extends BaseTracker
{
    /**
     * @var Timer
     */
    protected $timer;

    /**
     * @var Memory
     */
    protected $memory;

    /**
     * PerformanceTracker constructor.
     * @param Application $app
     * @param Timer $timer
     * @param Memory $memory
     * @param PerformanceListener $performanceListener
     */
    public function __construct(
        Application $app,
        Timer $timer,
        Memory $memory,
        PerformanceListener $performanceListener
    ) {
        parent::__construct($app);

        $this->timer = $timer;
        $this->memory = $memory;
        $performanceListener->listen();
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $this->data->put('performance', Collection::make([
            'timer' => $this->timer->all(),
            'memory' => $this->memory->all(),
        ]));
    }
}
