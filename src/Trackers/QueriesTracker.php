<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Foundation\Application;
use Illuminate\Database\Events\QueryExecuted;
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
        $queries = $this->queriesListener->queries()->map(function (QueryExecuted $event) {
            $sql = $this->formatSql($event->sql);

            return [
                'sql' => $sql,
                'bindings' => $event->bindings,
                'time' => $event->time,
                'database' => $event->connection->getDatabaseName(),
                'name' => $event->connectionName,
                'query' => $this->queryWithBindings($event, $sql),
            ];
        });

        $this->data->put('queries', $queries);
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
