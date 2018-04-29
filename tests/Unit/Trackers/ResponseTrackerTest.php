<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use Mockery;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Trackers\ResponseTracker;
use JKocik\Laravel\Profiler\Contracts\ExecutionResponse;

class ResponseTrackerTest extends TestCase
{
    /** @test */
    function has_response_meta()
    {
        $response = Mockery::mock(ExecutionResponse::class);
        $response->shouldReceive('meta')->andReturn(collect(['status' => 123]));
        $this->app->make(ExecutionData::class)->setResponse($response);

        $tracker = $this->app->make(ResponseTracker::class);
        $tracker->terminate();

        $this->assertEquals(123, $tracker->meta()->get('status'));
    }
}
