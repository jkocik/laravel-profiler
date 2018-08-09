<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\Timer;
use JKocik\Laravel\Profiler\Services\Timer\TimerService;
use JKocik\Laravel\Profiler\Services\Timer\NullTimerService;

class TimerTest extends TestCase
{
    /** @test */
    function enabled_profiler_has_singleton_timer()
    {
        $timerA = $this->app->make(Timer::class);
        $timerB = $this->app->make(Timer::class);

        $this->assertInstanceOf(TimerService::class, $timerA);
        $this->assertSame($timerA, $timerB);
    }

    /** @test */
    function disabled_profiler_has_singleton_null_timer()
    {
        putenv('PROFILER_ENABLED=false');
        $this->app = $this->app();
        $timerA = $this->app->make(Timer::class);
        $timerB = $this->app->make(Timer::class);

        $this->assertInstanceOf(NullTimerService::class, $timerA);
        $this->assertSame($timerA, $timerB);
    }

    /**
     * @return void
     */
    protected function tearDown()
    {
        parent::tearDown();

        putenv('PROFILER_ENABLED');
    }
}
