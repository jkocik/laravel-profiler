<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use App\Exceptions\Handler;
use JKocik\Laravel\Profiler\Events\ExceptionHandling;

class ExceptionHandler extends Handler
{
    /**
     * @param \Throwable $exception
     * @return void
     */
    public function report(\Throwable $exception)
    {
        event(new ExceptionHandling($exception));

        parent::report($exception);
    }
}
