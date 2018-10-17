<?php

namespace JKocik\Laravel\Profiler\Events;

use Exception;

class ExceptionHandling
{
    /**
     * @var Exception
     */
    public $exception;

    /**
     * ExceptionHandling constructor.
     * @param Exception $exception
     */
    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
    }
}
