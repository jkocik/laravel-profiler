<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Events\RouteMatched;
use JKocik\Laravel\Profiler\Events\Tracking;
use JKocik\Laravel\Profiler\Contracts\Timer;
use JKocik\Laravel\Profiler\Contracts\Memory;
use Illuminate\Console\Events\ArtisanStarting;
use JKocik\Laravel\Profiler\Events\Terminating;
use Illuminate\Foundation\Http\Events\RequestHandled;
use JKocik\Laravel\Profiler\Contracts\LaravelListener;

class PerformanceListener implements LaravelListener
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Timer
     */
    protected $timer;

    /**
     * @var Memory
     */
    protected $memory;

    /**
     * PerformanceListener constructor.
     * @param Application $app
     * @param Timer $timer
     * @param Memory $memory
     */
    public function __construct(
        Application $app,
        Timer $timer,
        Memory $memory
    ) {
        $this->app = $app;
        $this->timer = $timer;
        $this->memory = $memory;
    }

    /**
     * @return void
     */
    public function listen(): void
    {
        $this->listenHttp();
        $this->listenConsole();
    }

    /**
     * @return void
     */
    protected function listenHttp(): void
    {
        Event::listen(Tracking::class, function () {
            $this->timer->startLaravel();
        });

        $this->app->booting(function () {
            $this->timer->start('boot');
        });

        $this->app->booted(function () {
            $this->timer->finish('boot');
            $this->timer->start($this->resolveRouteName());
        });

        Event::listen(RouteMatched::class, function () {
            $this->timer->finish($this->resolveRouteName());
            $this->timer->start('request');
        });

        /** @codeCoverageIgnoreStart */
        Event::listen('kernel.handled', function () {
            $this->timer->finish('request');
            $this->timer->start('response');
        });
        /** @codeCoverageIgnoreEnd */

        Event::listen(RequestHandled::class, function () {
            $this->timer->finish('request');
            $this->timer->start('response');
        });

        Event::listen(Terminating::class, function () {
            $this->memory->recordPeak();
            $this->timer->finish('response');
            $this->timer->finishLaravel();
        });
    }

    /**
     * @return void
     */
    protected function listenConsole(): void
    {
        Event::listen(ArtisanStarting::class, function () {
            $this->timer->start('command');
        });

        Event::listen(Terminating::class, function () {
            $this->timer->finish('command');
        });
    }

    /**
     * @return string
     */
    protected function resolveRouteName(): string
    {
        if ($this->app->runningUnitTests()) {
            return 'setup';
        }

        return 'route';
    }
}
