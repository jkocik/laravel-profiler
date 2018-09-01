<?php

namespace JKocik\Laravel\Profiler\Services;

use Illuminate\Support\Collection;

class ParamsService
{
    /**
     * @param $param
     * @return mixed
     */
    public function resolve($param)
    {
        if (is_object($param)) {
            return $this->resolveObject($param);
        }

        if (is_array($param)) {
            return array_map(function ($item) {
                return $this->resolve($item);
            }, $param);
        }

        return $param;
    }

    /**
     * @param array $params
     * @return array
     */
    public function resolveFlattenFromArray(array $params): array
    {
        return array_map(function ($param) {
            return $this->resolveFlatten($param);
        }, $params);
    }

    /**
     * @param $param
     * @return string
     */
    protected function resolveFlatten($param): string
    {
        if ($param instanceof Collection) {
            return get_class($param) . ': ' . $param->count() . ' item(s)';
        }

        if (is_object($param)) {
            return get_class($param);
        }

        if (is_array($param)) {
            return 'array: ' . count($param) . ' item(s)';
        }

        return gettype($param);
    }

    /**
     * @param $param
     * @return array|string
     */
    protected function resolveObject($param)
    {
        if (method_exists($param, 'toArray')) {
            return $this->resolve($param->toArray());
        }

        return get_class($param);
    }
}
