<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\LaravelExecution;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\LaravelExecution\ConsoleStartingResponse;

class ConsoleStartingResponseTest extends TestCase
{
    /** @test */
    function returns_meta_and_data()
    {
        $consoleStartingResponse = new ConsoleStartingResponse();

        $this->assertNull($consoleStartingResponse->meta()->get('type'));
        $this->assertCount(0, $consoleStartingResponse->data());
    }
}
