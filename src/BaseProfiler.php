<?php

namespace JKocik\Laravel\Profiler;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Application;
use Illuminate\Console\Events\ArtisanStarting;
use JKocik\Laravel\Profiler\Contracts\Profiler;
use JKocik\Laravel\Profiler\Events\ResetTrackers;
use Illuminate\Foundation\Bootstrap\BootProviders;
use JKocik\Laravel\Profiler\Console\StatusCommand;
use JKocik\Laravel\Profiler\Console\ServerCommand;
use JKocik\Laravel\Profiler\Console\ClientCommand;

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
    public function resetTrackers(): void
    {
        event(new ResetTrackers());
    }

    /**
     * @return void
     */
    public function listenForBoot(): void
    {
        $this->app->beforeBootstrapping(BootProviders::class, function () {
            $this->commands();
            $this->boot();
        });
    }

    /**
     * @return void
     */
    abstract protected function boot(): void;

    /**
     * @return void
     */
    private function commands(): void
    {
        Event::listen(ArtisanStarting::class, function (ArtisanStarting $event) {
            $event->artisan->resolveCommands([
                StatusCommand::class,
                ServerCommand::class,
                ClientCommand::class,
            ]);
        });
    }
}
