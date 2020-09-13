<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\RedisTracker;

class RedisTrackerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    function has_executed_commands()
    {
        $tracker = $this->app->make(RedisTracker::class);
        $this->app->make('redis')->set('name', 'Laravel Profiler');

        $tracker->terminate();
        $redis = $tracker->data()->get('redis');

        $this->assertNotNull($redis);

        $this->tapLaravelVersionTill(5.6, function () use ($redis) {
            $this->assertEquals([], $redis->all());
        });
        $this->tapLaravelVersionFrom(5.7, function () use ($redis) {
            $this->assertEquals('set', $redis->first()['command']);
            $this->assertEquals('default', $redis->first()['name']);
            $this->assertEquals(['name', 'Laravel Profiler'], $redis->first()['parameters']);
            $this->assertArrayHasKey('time', $redis->first());
        });
    }

    /** @test */
    function can_count_commands()
    {
        $tracker = $this->app->make(RedisTracker::class);
        $this->app->make('redis')->set('name', 'Laravel Profiler');
        $this->app->make('redis')->set('action', 'testing');

        $tracker->terminate();

        $this->tapLaravelVersionTill(5.6, function () use ($tracker) {
            $this->assertEquals(0, $tracker->meta()->get('redis_count'));
        });
        $this->tapLaravelVersionFrom(5.7, function () use ($tracker) {
            $this->assertEquals(2, $tracker->meta()->get('redis_count'));
        });
    }

    /** @test */
    function knows_if_laravel_is_able_to_track_redis()
    {
        $tracker = $this->app->make(RedisTracker::class);

        $tracker->terminate();

        $this->tapLaravelVersionTill(5.6, function () use ($tracker) {
            $this->assertFalse($tracker->meta()->get('redis_can_be_tracked'));
        });
        $this->tapLaravelVersionFrom(5.7, function () use ($tracker) {
            $this->assertTrue($tracker->meta()->get('redis_can_be_tracked'));
        });
    }

    /** @test */
    function can_reset_commands()
    {
        $tracker = $this->app->make(RedisTracker::class);
        $this->app->make('redis')->set('name', 'Laravel Profiler');
        $this->app->make('redis')->set('name', 'Laravel Profiler');
        $this->app->make('redis')->set('name', 'Laravel Profiler');

        profiler_reset();

        $this->app->make('redis')->set('name', 'Laravel Profiler');

        $tracker->terminate();

        $this->tapLaravelVersionTill(5.6, function () {
            $this->assertTrue(true);
        });
        $this->tapLaravelVersionFrom(5.7, function () use ($tracker) {
            $this->assertEquals(1, $tracker->meta()->get('redis_count'));
            $this->assertCount(1, $tracker->data()->get('redis'));
        });
    }
}
