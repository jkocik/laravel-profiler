<?php

namespace JKocik\Laravel\Profiler;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\Tracker;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Services\ConfigService;
use JKocik\Laravel\Profiler\Trackers\RequestTracker;
use JKocik\Laravel\Profiler\Trackers\ResponseTracker;
use JKocik\Laravel\Profiler\Trackers\ApplicationTracker;
use JKocik\Laravel\Profiler\Trackers\PerformanceTracker;

class LaravelDataTracker implements DataTracker
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * @var Collection
     */
    protected $trackers;

    /**
     * @var Collection
     */
    protected $meta;

    /**
     * @var Collection
     */
    protected $data;

    /**
     * LaravelDataTracker constructor.
     * @param Application $app
     * @param ConfigService $configService
     */
    public function __construct(Application $app, ConfigService $configService)
    {
        $this->app = $app;
        $this->configService = $configService;

        $this->trackers = new Collection();
        $this->meta = new Collection();
        $this->data = new Collection();
    }

    /**
     * @return void
     */
    public function track(): void
    {
        $this->bootTrackers(Collection::make([
            ApplicationTracker::class,
            PerformanceTracker::class,
            RequestTracker::class,
            ResponseTracker::class,
        ]));

        $this->bootTrackers($this->configService->trackers());
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $this->trackers->each(function (Tracker $tracker) {
            $tracker->terminate();
            $this->meta = $this->meta->merge($tracker->meta());
            $this->data = $this->data->merge($tracker->data());
        });
    }

    /**
     * @return Collection
     */
    public function meta(): Collection
    {
        return $this->meta;
    }

    /**
     * @return Collection
     */
    public function data(): Collection
    {
        return $this->data;
    }

    /**
     * @param Collection $trackers
     * @return void
     */
    protected function bootTrackers(Collection $trackers): void
    {
        $trackers->each(function (string $tracker) {
            $this->trackers->push($this->app->make($tracker));
        });
    }
}
