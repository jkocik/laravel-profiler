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
            list($bindings, $bindingsQuoted) = $this->formatBindings($event);

            array_push($this->queries, [
                $event->sql,
                $event->time,
                $event->connection->getDatabaseName(),
                $event->connectionName,
                $bindings,
                $bindingsQuoted,
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
    protected function formatBindings(QueryExecuted $event): array
    {
        foreach ($event->bindings as $key => $binding) {
            $bindings[$key] = $this->truncate($binding);
            $bindingsQuoted[$key] = $this->quote($event, $bindings[$key]);
        }

        return [
            $bindings ?? [],
            $bindingsQuoted ?? [],
        ];
    }

    /**
     * @param $binding
     * @return mixed
     */
    protected function truncate($binding)
    {
        if (is_string($binding) && strlen($binding) > 255) {
            return substr($binding, 0, 255) . '...{truncated}';
        }

        return $binding;
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

        return $event->connection->getPdo()->quote($binding);
    }
}
