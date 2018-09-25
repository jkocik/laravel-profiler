<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Services\ConfigService;
use JKocik\Laravel\Profiler\Services\ParamsService;
use JKocik\Laravel\Profiler\LaravelListeners\EventsListener;

class EventsTracker extends BaseTracker
{
    /**
     * @var ParamsService
     */
    protected $paramsService;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * @var EventsListener
     */
    protected $eventsListener;

    /**
     * EventsTracker constructor.
     * @param Application $app
     * @param ParamsService $paramsService
     * @param ConfigService $configService
     * @param EventsListener $eventsListener
     */
    public function __construct(
        Application $app,
        ParamsService $paramsService,
        ConfigService $configService,
        EventsListener $eventsListener
    ) {
        parent::__construct($app);

        $this->paramsService = $paramsService;
        $this->configService = $configService;
        $this->eventsListener = $eventsListener;
        $this->eventsListener->listen();
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $events = $this->eventsListener->events()->map(function ($item) {
            list($event, $payload, $name, $count) = $item;

            if ($this->shouldTrackData($count)) {
                return [
                    'name' => $name,
                    'count' => $count,
                    'data' => $this->resolveData($event, $payload),
                ];
            }

            return [
                'name' => $name,
                'count' => $count,
            ];
        });

        $this->meta->put('events_count', $this->eventsListener->count());
        $this->data->put('events', $events);
    }

    /**
     * @param int $count
     * @return bool
     */
    protected function shouldTrackData(int $count): bool
    {
        return $this->configService->isEventsDataEnabled() && $count == 1;
    }

    /**
     * @param $event
     * @param $payload
     * @return Collection
     */
    protected function resolveData($event, $payload): Collection
    {
        $event = is_array($payload) ? $payload[0] : $event;

        $class = new \ReflectionClass($event);

        $publicProps = Collection::make(
            $class->getProperties(\ReflectionProperty::IS_PUBLIC)
        );

        if (method_exists($publicProps, 'mapWithKeys')) {
            return $this->propsMapWithKeys($publicProps, $event);
        }

        return $this->propsMap($publicProps, $event); // @codeCoverageIgnore
    }

    /**
     * @param Collection $publicProps
     * @param $event
     * @return Collection
     */
    protected function propsMapWithKeys(Collection $publicProps, $event): Collection
    {
        return $publicProps->mapWithKeys(function ($prop) use ($event) {
            return [
                $prop->name => $this->paramsService->resolve($event->{$prop->name}),
            ];
        });
    }

    /**
     * @codeCoverageIgnore
     *
     * @param Collection $publicProps
     * @param $event
     * @return Collection
     */
    protected function propsMap(Collection $publicProps, $event): Collection
    {
        return $publicProps->map(function ($prop) use ($event) {
            return [
                $prop->name => $this->paramsService->resolve($event->{$prop->name}),
            ];
        })->flatten(1);
    }
}
