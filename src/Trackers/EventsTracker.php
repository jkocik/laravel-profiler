<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\LaravelListeners\EventsListener;

class EventsTracker extends BaseTracker
{
    /**
     * @var EventsListener
     */
    protected $eventsListener;

    /**
     * EventsTracker constructor.
     * @param Application $app
     * @param EventsListener $eventsListener
     */
    public function __construct(Application $app, EventsListener $eventsListener)
    {
        parent::__construct($app);

        $this->eventsListener = $eventsListener;
        $this->eventsListener->listen();
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $events = $this->eventsListener->events()->values();

        $this->data->put('events', $events);
    }
}
