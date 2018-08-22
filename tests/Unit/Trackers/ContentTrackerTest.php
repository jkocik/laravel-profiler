<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use Mockery;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\ContentTracker;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionContent;

class ContentTrackerTest extends TestCase
{
    /** @test */
    function has_content_meta()
    {
        $content = Mockery::mock(ExecutionContent::class)->shouldIgnoreMissing();
        $content->shouldReceive('meta')->once()->andReturn(collect([
            'key-a' => 'val-a',
            'key-b' => 'val-b',
            'key-c' => 'val-c',
        ]));
        $content->shouldReceive('data')->andReturn(collect());
        $this->app->make(ExecutionData::class)->setContent($content);

        $tracker = $this->app->make(ContentTracker::class);
        $tracker->terminate();

        $this->assertEquals('val-a', $tracker->meta()->get('key-a'));
        $this->assertEquals('val-b', $tracker->meta()->get('key-b'));
        $this->assertEquals('val-c', $tracker->meta()->get('key-c'));
    }

    /** @test */
    function has_content_data()
    {
        $content = Mockery::mock(ExecutionContent::class)->shouldIgnoreMissing();
        $content->shouldReceive('data')->once()->andReturn(collect([
            'content' => '<html><body></body></html>',
        ]));
        $this->app->make(ExecutionData::class)->setContent($content);

        $tracker = $this->app->make(ContentTracker::class);
        $tracker->terminate();

        $this->assertEquals('<html><body></body></html>', $tracker->data()->get('content'));
    }
}
