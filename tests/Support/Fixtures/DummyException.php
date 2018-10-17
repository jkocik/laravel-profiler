<?php

namespace JKocik\Laravel\Profiler\Tests\Support\Fixtures;

use Exception;

class DummyException extends Exception
{
    /**
     * @return void
     */
    public function report()
    {

    }
}
