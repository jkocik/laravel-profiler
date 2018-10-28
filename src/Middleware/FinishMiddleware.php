<?php

namespace JKocik\Laravel\Profiler\Middleware;

use Closure;
use JKocik\Laravel\Profiler\Events\MiddlewareFinished;

class FinishMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        event(new MiddlewareFinished());

        return $next($request);
    }
}
