<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\LaravelListeners\QueriesListener;

class QueriesTracker extends BaseTracker
{
    /**
     * @var QueriesListener
     */
    protected $queriesListener;

    /**
     * QueriesTracker constructor.
     * @param Application $app
     * @param QueriesListener $queriesListener
     */
    public function __construct(Application $app, QueriesListener $queriesListener)
    {
        parent::__construct($app);

        $this->queriesListener = $queriesListener;
        $this->queriesListener->listen();
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $queries = $this->queriesListener->queries()->values();

        $this->data->put('queries', $queries);
    }
}
