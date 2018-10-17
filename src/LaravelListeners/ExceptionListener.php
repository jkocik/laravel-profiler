<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use Exception;
use Illuminate\Support\Facades\Event;
use JKocik\Laravel\Profiler\Events\ExceptionHandling;
use JKocik\Laravel\Profiler\Contracts\LaravelListener;

class ExceptionListener implements LaravelListener
{
    /**
     * @var Exception
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
     * @return Exception|null
     */
    public function exception(): ?Exception
    {
        return $this->exception;
    }
}
