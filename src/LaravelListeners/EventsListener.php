<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;
use JKocik\Laravel\Profiler\Events\ResetTrackers;
use JKocik\Laravel\Profiler\Services\ConfigService;
use JKocik\Laravel\Profiler\Contracts\LaravelListener;

class EventsListener implements LaravelListener
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * @var array
     */
    protected $events = [];

    /**
     * @var string
     */
    protected $previousEventName = '';

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * EventsListener constructor.
     * @param Dispatcher $dispatcher
     * @param ConfigService $configService
     */
    public function __construct(Dispatcher $dispatcher, ConfigService $configService)
    {
        $this->dispatcher = $dispatcher;
        $this->configService = $configService;
    }

    /**
     * @return void
     */
    public function listen(): void
    {
        $this->listenEvents();
        $this->listenResetTrackers();
    }

    /**
     * @return Collection
     */
    public function events(): Collection
    {
        return Collection::make($this->events);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * @return void
     */
    protected function listenEvents(): void
    {
        $this->dispatcher->listen('*', function ($event, $payload = null) {
            $name = $this->resolveName($event, $payload);

            if ($this->shouldSkip($name)) {
                return;
            }

            $this->count++;

            if ($this->shouldGroup($name)) {
                return $this->groupToPreviousEvent();
            }

            $this->previousEventName = $name;

            array_push($this->events, $this->resolveEvent($name, $event, $payload));
        });
    }

    /**
     * @return void
     */
    protected function listenResetTrackers(): void
    {
        $this->dispatcher->listen(ResetTrackers::class, function () {
            $this->events = [];
            $this->previousEventName = '';
            $this->count = 0;
        });
    }

    /**
     * @param string $name
     * @param $event
     * @param $payload
     * @return array
     */
    protected function resolveEvent(string $name, $event, $payload): array
    {
        if ($this->configService->isEventsDataEnabled()) {
            return [$event, $payload, $name, 1];
        }

        return [null, null, $name, 1];
    }

    /**
     * @param $event
     * @param $payload
     * @return string
     */
    protected function resolveName($event, $payload): string
    {
        return is_array($payload) ? $event : $this->dispatcher->firing();
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function shouldGroup(string $name): bool
    {
        return $this->configService->isEventsGroupEnabled() && $name == $this->previousEventName;
    }

    /**
     * @return void
     */
    protected function groupToPreviousEvent(): void
    {
        $this->events[count($this->events) - 1][3]++;
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function shouldSkip(string $name): bool
    {
        $shouldSkip = Collection::make([
            'bootstrapped: ' . \Illuminate\Foundation\Bootstrap\BootProviders::class,
            \JKocik\Laravel\Profiler\Events\ExceptionHandling::class,
            \JKocik\Laravel\Profiler\Events\ProfilerBound::class,
            \JKocik\Laravel\Profiler\Events\ResetTrackers::class,
            \JKocik\Laravel\Profiler\Events\Terminating::class,
            \JKocik\Laravel\Profiler\Events\Tracking::class,
        ]);

        return $shouldSkip->contains($name);
    }
}
