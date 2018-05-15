<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use Mockery;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\RequestTracker;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionRequest;

class RequestTrackerTest extends TestCase
{
    /** @test */
    function has_request_meta()
    {
        $request = Mockery::mock(ExecutionRequest::class)->shouldIgnoreMissing();
        $request->shouldReceive('meta')->once()->andReturn(collect([
            'key-a' => 'val-a',
            'key-b' => 'val-b',
            'key-c' => 'val-c',
        ]));
        $this->app->make(ExecutionData::class)->setRequest($request);

        $tracker = $this->app->make(RequestTracker::class);
        $tracker->terminate();

        $this->assertEquals('val-a', $tracker->meta()->get('key-a'));
        $this->assertEquals('val-b', $tracker->meta()->get('key-b'));
        $this->assertEquals('val-c', $tracker->meta()->get('key-c'));
    }

    /** @test */
    function has_request_data()
    {
        $request = Mockery::mock(ExecutionRequest::class)->shouldIgnoreMissing();
        $request->shouldReceive('data')->once()->andReturn(collect([
            ['name' => 'key-x', 'value' => 'val-x'],
            ['name' => 'key-y', 'value' => 'val-y'],
            ['name' => 'key-z', 'value' => 'val-z'],
        ]));
        $this->app->make(ExecutionData::class)->setRequest($request);

        $tracker = $this->app->make(RequestTracker::class);
        $tracker->terminate();

        $this->assertEquals('val-x', $tracker->data()->get('request')->where('name', 'key-x')->first()['value']);
        $this->assertEquals('val-y', $tracker->data()->get('request')->where('name', 'key-y')->first()['value']);
        $this->assertEquals('val-z', $tracker->data()->get('request')->where('name', 'key-z')->first()['value']);
    }
}
