<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Exception;
use App\Exceptions\Handler;
use JKocik\Laravel\Profiler\Events\ExceptionHandling;

class ExceptionHandlerTillVersion6 extends Handler
{
    /**
     * @param Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        event(new ExceptionHandling($exception));

        parent::report($exception);
    }
}
