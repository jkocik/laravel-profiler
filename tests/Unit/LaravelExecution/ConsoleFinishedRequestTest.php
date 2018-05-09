<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\LaravelExecution;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\LaravelExecution\ConsoleFinishedRequest;

class ConsoleFinishedRequestTest extends TestCase
{
    /** @test */
    function allows_command_to_be_null()
    {
        $consoleFinishedRequest = new ConsoleFinishedRequest(null);

        $this->assertNull($consoleFinishedRequest->meta()->get('path'));
    }
}
