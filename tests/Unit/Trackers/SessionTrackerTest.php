<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use Mockery;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\SessionTracker;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionSession;

class SessionTrackerTest extends TestCase
{
    /** @test */
    function has_session_meta()
    {
        $session = Mockery::mock(ExecutionSession::class)->shouldIgnoreMissing();
        $session->shouldReceive('meta')->once()->andReturn(collect([
            'key-a' => 'val-a',
            'key-b' => 'val-b',
            'key-c' => 'val-c',
        ]));
        $this->app->make(ExecutionData::class)->setSession($session);

        $tracker = $this->app->make(SessionTracker::class);
        $tracker->terminate();

        $this->assertEquals('val-a', $tracker->meta()->get('key-a'));
        $this->assertEquals('val-b', $tracker->meta()->get('key-b'));
        $this->assertEquals('val-c', $tracker->meta()->get('key-c'));
    }

    /** @test */
    function has_session_data()
    {
        $session = Mockery::mock(ExecutionSession::class)->shouldIgnoreMissing();
        $session->shouldReceive('data')->once()->andReturn(collect([
            ['name' => 'key-x', 'value' => 'val-x'],
            ['name' => 'key-y', 'value' => 'val-y'],
            ['name' => 'key-z', 'value' => 'val-z'],
        ]));
        $this->app->make(ExecutionData::class)->setSession($session);

        $tracker = $this->app->make(SessionTracker::class);
        $tracker->terminate();

        $this->assertEquals('val-x', $tracker->data()->get('session')->where('name', 'key-x')->first()['value']);
        $this->assertEquals('val-y', $tracker->data()->get('session')->where('name', 'key-y')->first()['value']);
        $this->assertEquals('val-z', $tracker->data()->get('session')->where('name', 'key-z')->first()['value']);
    }
}
