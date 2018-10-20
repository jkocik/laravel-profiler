<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Events\RouteMatched;
use JKocik\Laravel\Profiler\Contracts\Timer;
use Illuminate\Foundation\Http\Events\RequestHandled;
use JKocik\Laravel\Profiler\Contracts\LaravelListener;

class TimerListener implements LaravelListener
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
     * TimerListener constructor.
     * @param Application $app
     * @param Timer $timer
     */
    public function __construct(Application $app, Timer $timer)
    {
        $this->app = $app;
        $this->timer = $timer;
    }

    /**
     * @return void
     */
    public function listen(): void
    {
        $this->app->booting(function () {
            $this->timer->start('boot');
        });

        $this->app->booted(function () {
            $this->timer->finish('boot');
            $this->timer->start('middleware');
        });

        Event::listen(RouteMatched::class, function () {
            $this->timer->finish('middleware');
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
    }
}
