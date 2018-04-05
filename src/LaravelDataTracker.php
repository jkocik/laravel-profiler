<?php

namespace JKocik\Laravel\Profiler;

use Illuminate\Support\Collection;
use JKocik\Laravel\Profiler\Contracts\Tracker;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Services\ConfigService;
use JKocik\Laravel\Profiler\Trackers\ApplicationTracker;

class LaravelDataTracker implements DataTracker
{
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
     * @var ConfigService
     */
    protected $configService;

    /**
     * LaravelDataTracker constructor.
     * @param ConfigService $configService
     */
    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;

        $this->trackers = new Collection([
            app()->make(ApplicationTracker::class),
        ]);

        $this->meta = new Collection();
        $this->data = new Collection();
    }

    /**
     * @return void
     */
    public function track(): void
    {
        array_map(function ($tracker) {
            $this->trackers->push(
                app()->make($tracker)
            );
        }, $this->configService->trackers());
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
}
