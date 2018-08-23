<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;
use JKocik\Laravel\Profiler\Contracts\LaravelListener;

class EventsListener implements LaravelListener
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var array
     */
    protected $event = [];

    /**
     * @var array
     */
    protected $payload = [];

    /**
     * @var array
     */
    protected $name = [];

    /**
     * EventsListener constructor.
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return void
     */
    public function listen(): void
    {
        $this->dispatcher->listen('*', function ($event, $payload = null) {
            $this->event[] = $event;
            $this->payload[] = $payload;
            $this->name[] = $this->resolveName($event, $payload);
        });
    }

    /**
     * @return Collection
     */
    public function events(): Collection
    {
        return Collection::make($this->event)->zip($this->payload, $this->name);
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
}
