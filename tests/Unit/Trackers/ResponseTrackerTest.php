<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use Mockery;
use JKocik\Laravel\Profiler\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;
use JKocik\Laravel\Profiler\Contracts\DataService;
use JKocik\Laravel\Profiler\Trackers\ResponseTracker;

class ResponseTrackerTest extends TestCase
{
    /** @test */
    function has_status()
    {
        $response = Mockery::mock(Response::class)->shouldIgnoreMissing();
        $response->shouldReceive('status')->andReturn('201')->once();
        $tracker = $this->app->make(ResponseTracker::class);

        $this->app->make(DataService::class)->setResponse($response);
        $tracker->terminate();

        $this->assertTrue($tracker->meta()->has('status'));
        $this->assertEquals(201, $tracker->meta()->get('status'));
    }
}
