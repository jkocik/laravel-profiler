<?php

namespace JKocik\Laravel\Profiler\Events;

class ExceptionHandling
{
    /**
     * @var mixed
     */
    public $exception;

    /**
     * ExceptionHandling constructor.
     * @param $exception
     */
    public function __construct($exception)
    {
        $this->exception = $exception;
    }
}
