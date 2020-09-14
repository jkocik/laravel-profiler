<?php

namespace JKocik\Laravel\Profiler\Events;

use Throwable;

class ExceptionHandling
{
    /**
     * @var Throwable
     */
    public $exception;

    /**
     * ExceptionHandling constructor.
     * @param Throwable $exception
     */
    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }
}
