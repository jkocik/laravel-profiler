<?php

namespace JKocik\Laravel\Profiler;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\Profiler;
use Illuminate\Foundation\Bootstrap\BootProviders;

abstract class BaseProfiler implements Profiler
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * BaseProfiler constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return void
     */
    public function listenForBoot(): void
    {
        $this->app['events']->listen('bootstrapping: ' . BootProviders::class, function () {
            $this->boot();
        });
    }

    /**
     * @return void
     */
    abstract protected function boot(): void;
}
