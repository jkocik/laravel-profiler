<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Services\ParamsService;
use JKocik\Laravel\Profiler\LaravelListeners\EventsListener;

class EventsTracker extends BaseTracker
{
    /**
     * @var ParamsService
     */
    protected $paramsService;

    /**
     * @var EventsListener
     */
    protected $eventsListener;

    /**
     * EventsTracker constructor.
     * @param Application $app
     * @param ParamsService $paramsService
     * @param EventsListener $eventsListener
     */
    public function __construct(Application $app, ParamsService $paramsService, EventsListener $eventsListener)
    {
        parent::__construct($app);

        $this->paramsService = $paramsService;
        $this->eventsListener = $eventsListener;
        $this->eventsListener->listen();
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $events = $this->eventsListener->events()->map(function ($item) {
            $data = $this->resolveData($item[0], $item[1]);

            return [
                'name' => $item[2],
                'data' => $data,
            ];
        });

        $this->data->put('events', $events);
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

        return $this->propsMap($publicProps, $event);
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
