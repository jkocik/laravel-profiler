<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use Throwable;
use Illuminate\Support\Facades\Event;
use JKocik\Laravel\Profiler\Events\ExceptionHandling;
use JKocik\Laravel\Profiler\Contracts\LaravelListener;

class ExceptionListener implements LaravelListener
{
    /**
     * @var Throwable
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

    public function exception(): ?Throwable
    {
        return $this->exception;
    }
}
