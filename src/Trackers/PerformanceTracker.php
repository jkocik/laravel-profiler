<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\Timer;
use JKocik\Laravel\Profiler\Contracts\Memory;
use JKocik\Laravel\Profiler\LaravelListeners\TimerListener;

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
     * @var TimerListener
     */
    protected $timerListener;

    /**
     * PerformanceTracker constructor.
     * @param Application $app
     * @param Timer $timer
     * @param Memory $memory
     * @param TimerListener $timerListener
     */
    public function __construct(
        Application $app,
        Timer $timer,
        Memory $memory,
        TimerListener $timerListener
    ) {
        parent::__construct($app);

        $this->timer = $timer;
        $this->memory = $memory;
        $this->timerListener = $timerListener;
        $this->timerListener->listen();
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
