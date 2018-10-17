<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;
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
        $this->dispatcher->listen('*', function ($event, $payload = null) {
            $name = $this->resolveName($event, $payload);

            if ($this->isLaravelProfilerInternalEvent($name)) {
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
    protected function isLaravelProfilerInternalEvent(string $name): bool
    {
        $laravelProfilerInternalEvents = Collection::make([
            \JKocik\Laravel\Profiler\Events\ExceptionHandling::class,
        ]);

        return $laravelProfilerInternalEvents->contains($name);
    }
}
