<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use Exception;
use Illuminate\Support\Facades\Event;
use JKocik\Laravel\Profiler\Events\ExceptionHandling;
use JKocik\Laravel\Profiler\Contracts\LaravelListener;

class ExceptionListener implements LaravelListener
{
    /**
     * @var mixed
     */
    protected $exception;

    /**
     * @return void
     */
    public function listen(): void
    {
        Event::listen(ExceptionHandling::class, function (ExceptionHandling $exceptionHandling) {
            $this->exception = $exceptionHandling->exception;
        });
    }

    /**
     * @return mixed
     */
    public function exception()
    {
        return $this->exception;
    }
}
