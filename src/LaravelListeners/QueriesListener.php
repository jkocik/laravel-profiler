<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Events\QueryExecuted;
use JKocik\Laravel\Profiler\Contracts\LaravelListener;

class QueriesListener implements LaravelListener
{
    /**
     * @var Collection
     */
    protected $queries;

    /**
     * QueriesListener constructor.
     */
    public function __construct()
    {
        $this->queries = new Collection();
    }

    /**
     * @return void
     */
    public function listen(): void
    {
        Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
            $sql = $this->formatSql($event->sql);

            $this->queries->push([
                'sql' => $sql,
                'bindings' => $event->bindings,
                'time' => $event->time,
                'database' => $event->connection->getDatabaseName(),
                'name' => $event->connectionName,
                'query' => $this->queryWithBindings($event, $sql),
            ]);
        });
    }

    /**
     * @return Collection
     */
    public function queries(): Collection
    {
        return $this->queries;
    }

    /**
     * @param string $sql
     * @return string
     */
    protected function formatSql(string $sql): string
    {
        return preg_replace('/"/', '`', $sql);
    }

    /**
     * @param QueryExecuted $event
     * @param string $sql
     * @return string
     */
    protected function queryWithBindings(QueryExecuted $event, string $sql): string
    {
        foreach ($event->bindings as $key => $binding) {
            $sql = preg_replace($this->bindingRegex($key), $this->bindingValue($event, $binding), $sql, 1);
        }

        return $sql;
    }

    /**
     * @param $key
     * @return string
     */
    protected function bindingRegex($key): string
    {
        return is_int($key) ? "/\?/" : "/:{$key}/";
    }

    /**
     * @param QueryExecuted $event
     * @param $binding
     * @return mixed
     */
    protected function bindingValue(QueryExecuted $event, $binding)
    {
        if (is_int($binding) || is_float($binding)) {
            return $binding;
        }

        return $event->connection->getPdo()->quote($binding);
    }
}
