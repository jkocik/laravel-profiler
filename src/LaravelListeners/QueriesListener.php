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
            array_push($this->queries, [
                $event->sql,
                $event->time,
                $event->connection->getDatabaseName(),
                $event->connectionName,
                $event->bindings,
                $this->bindingsQuoted($event),
            ]);
        });
    }

    /**
     * @return Collection
     */
    public function queries(): Collection
    {
        return Collection::make($this->queries);
    }

    /**
     * @param QueryExecuted $event
     * @return array
     */
    protected function bindingsQuoted(QueryExecuted $event): array
    {
        return array_map(function ($binding) use ($event) {
            return $this->quote($event, $binding);
        }, $event->bindings);
    }

    /**
     * @param QueryExecuted $event
     * @param $binding
     * @return mixed
     */
    protected function quote(QueryExecuted $event, $binding)
    {
        if (is_int($binding) || is_float($binding)) {
            return $binding;
        }

        if (is_object($binding)) {
            return '{object}';
        }

        if (strlen($binding) > 255) {
            return $event->connection->getPdo()->quote(
                substr($binding, 0, 255) . '...{truncated}'
            );
        }

        return $event->connection->getPdo()->quote($binding);
    }
}
