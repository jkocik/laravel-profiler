<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Events\QueryExecuted;
use JKocik\Laravel\Profiler\Contracts\LaravelListener;

class QueriesListener implements LaravelListener
{
    /**
     * @var array
     */
    protected $queries = [];

    /**
     * @return void
     */
    public function listen(): void
    {
        Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
            array_push($this->queries, $event);
        });
    }

    /**
     * @return Collection
     */
    public function queries(): Collection
    {
        return Collection::make($this->queries);
    }
}
