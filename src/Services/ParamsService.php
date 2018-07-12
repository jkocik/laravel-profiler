<?php

namespace JKocik\Laravel\Profiler\Services;

class ParamsService
{
    /**
     * @param $param
     * @return array|string
     */
    public function resolve($param)
    {
        if ($this->isObjectWithToArrayMethod($param)) {
            return $param->toArray();
        }

        if ($this->isObject($param)) {
            return get_class($param);
        }

        if ($this->isArray($param)) {
            return array_map(function ($item) {
                return $this->resolve($item);
            }, $param);
        }

        return $param;
    }

    /**
     * @param $param
     * @return bool
     */
    protected function isObjectWithToArrayMethod($param): bool
    {
        return is_object($param) && method_exists($param, 'toArray');
    }

    /**
     * @param $param
     * @return bool
     */
    protected function isObject($param): bool
    {
        return is_object($param);
    }

    /**
     * @param $param
     * @return bool
     */
    protected function isArray($param): bool
    {
        return is_array($param);
    }
}
