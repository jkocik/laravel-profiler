<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\LaravelExecution;

use Mockery;
use JKocik\Laravel\Profiler\Tests\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use JKocik\Laravel\Profiler\LaravelExecution\ConsoleFinishedRequest;

class ConsoleFinishedRequestTest extends TestCase
{
    /** @test */
    function allows_command_to_be_null()
    {
        $input = Mockery::mock(InputInterface::class);

        $consoleFinishedRequest = new ConsoleFinishedRequest(null, $input);

        $this->assertNull($consoleFinishedRequest->meta()->get('path'));
    }
}
