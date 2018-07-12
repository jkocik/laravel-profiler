<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;
use JKocik\Laravel\Profiler\Services\ParamsService;
use JKocik\Laravel\Profiler\Contracts\LaravelListener;

class EventsListener implements LaravelListener
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var ParamsService
     */
    protected $paramsService;

    /**
     * @var Collection
     */
    protected $events;

    /**
     * EventsListener constructor.
     * @param Dispatcher $dispatcher
     * @param ParamsService $paramsService
     */
    public function __construct(Dispatcher $dispatcher, ParamsService $paramsService)
    {
        $this->dispatcher = $dispatcher;
        $this->paramsService = $paramsService;
        $this->events = new Collection();
    }

    /**
     * @return void
     */
    public function listen(): void
    {
        $this->dispatcher->listen('*', function ($event, $payload = null) {
            $name = $this->resolveName($event, $payload);
            $data = $this->resolveData($event, $payload);

            $this->events->push([
                'name' => $name,
                'data' => $data,
            ]);
        });
    }

    /**
     * @return Collection
     */
    public function events(): Collection
    {
        return $this->events;
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
     * @param $event
     * @param $payload
     * @return Collection
     */
    protected function resolveData($event, $payload): Collection
    {
        return $this->data(is_array($payload) ? $payload[0] : $event);
    }

    /**
     * @param $event
     * @return Collection
     */
    protected function data($event): Collection
    {
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
