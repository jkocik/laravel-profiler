<?php

namespace JKocik\Laravel\Profiler\Services;

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
