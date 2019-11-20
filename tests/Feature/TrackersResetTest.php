<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Events\ResetTrackers;

class TrackersResetTest extends TestCase
{
    /** @test */
    function trackers_can_be_reset()
    {
        $fired = false;

        Event::listen(ResetTrackers::class, function () use (&$fired) {
            $fired = true;
        });

        $this->assertFalse($fired);

        profiler_reset();

        $this->assertTrue($fired);
    }

    /** @test */
    function profiler_reset_function_can_be_executed_even_profiler_is_disabled()
    {
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.enabled', false);
        });

        $fired = false;

        Event::listen(ResetTrackers::class, function () use (&$fired) {
            $fired = true;
        });

        $this->assertFalse($fired);

        profiler_reset();

        $this->assertTrue($fired);
    }
}
