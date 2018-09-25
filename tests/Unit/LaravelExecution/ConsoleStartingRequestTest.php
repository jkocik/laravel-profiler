<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\LaravelExecution;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\LaravelExecution\ConsoleStartingRequest;

class ConsoleStartingRequestTest extends TestCase
{
    /** @test */
    function returns_meta_and_data()
    {
        $consoleStartingRequest = new ConsoleStartingRequest();

        $this->assertEquals('command-starting', $consoleStartingRequest->meta()->get('type'));
        $this->assertCount(0, $consoleStartingRequest->data());
    }
}
