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
        $queries = $this->queriesListener->queries()->map(function ($item) {
            if ($this->isTransactionType($item[0])) {
                return $this->terminateTransaction($item);
            }

            return $this->terminateQuery($item);
        });

        $this->meta->put('queries_count', $this->queriesListener->count());
        $this->data->put('queries', $queries);
    }

    /**
     * @param array $item
     * @return array
     */
    protected function terminateTransaction(array $item): array
    {
        list($type, $database, $name) = $item;

        return [
            'type' => $type,
            'database' => $database,
            'name' => $name,
        ];
    }

    /**
     * @param array $item
     * @return array
     */
    protected function terminateQuery(array $item): array
    {
        list($type, $sql, $time, $database, $name, $bindings, $bindingsQuoted) = $item;

        $formattedSql = $this->formatSql($sql);

        return [
            'type' => $type,
            'sql' => $formattedSql,
            'bindings' => $bindings,
            'time' => $time,
            'database' => $database,
            'name' => $name,
            'query' => $this->queryWithBindings($bindingsQuoted, $formattedSql),
        ];
    }

    /**
     * @param string $type
     * @return bool
     */
    protected function isTransactionType(string $type): bool
    {
        $transactionTypes = [
            'transaction-begin',
            'transaction-commit',
            'transaction-rollback',
        ];

        return in_array($type, $transactionTypes);
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
     * @param array $bindingsQuoted
     * @param string $formattedSql
     * @return string
     */
    protected function queryWithBindings(array $bindingsQuoted, string $formattedSql): string
    {
        foreach ($bindingsQuoted as $key => $binding) {
            $formattedSql = preg_replace($this->bindingRegex($key), $binding, $formattedSql, 1);
        }

        return $formattedSql;
    }

    /**
     * @param $key
     * @return string
     */
    protected function bindingRegex($key): string
    {
        return is_int($key) ? "/\?/" : "/:{$key}/";
    }
}
