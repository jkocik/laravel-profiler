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
        $response = Mockery::mock(ExecutionResponse::class)->shouldIgnoreMissing();
        $response->shouldReceive('meta')->once()->andReturn(collect([
            'key-a' => 'val-a',
            'key-b' => 'val-b',
            'key-c' => 'val-c',
        ]));
        $this->app->make(ExecutionData::class)->setResponse($response);

        $tracker = $this->app->make(ResponseTracker::class);
        $tracker->terminate();

        $this->assertEquals('val-a', $tracker->meta()->get('key-a'));
        $this->assertEquals('val-b', $tracker->meta()->get('key-b'));
        $this->assertEquals('val-c', $tracker->meta()->get('key-c'));
    }

    /** @test */
    function has_response_data()
    {
        $response = Mockery::mock(ExecutionResponse::class)->shouldIgnoreMissing();
        $response->shouldReceive('data')->once()->andReturn(collect([
            ['name' => 'key-x', 'value' => 'val-x'],
            ['name' => 'key-y', 'value' => 'val-y'],
            ['name' => 'key-z', 'value' => 'val-z'],
        ]));
        $this->app->make(ExecutionData::class)->setResponse($response);

        $tracker = $this->app->make(ResponseTracker::class);
        $tracker->terminate();

        $this->assertEquals('val-x', $tracker->data()->get('response')->where('name', 'key-x')->first()['value']);
        $this->assertEquals('val-y', $tracker->data()->get('response')->where('name', 'key-y')->first()['value']);
        $this->assertEquals('val-z', $tracker->data()->get('response')->where('name', 'key-z')->first()['value']);
    }
}
