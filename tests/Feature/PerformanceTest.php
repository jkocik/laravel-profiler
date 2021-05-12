<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\Timer;
use JKocik\Laravel\Profiler\Contracts\Memory;
use JKocik\Laravel\Profiler\Services\Performance\TimerService;
use JKocik\Laravel\Profiler\Services\Performance\NullTimerService;

class PerformanceTest extends TestCase
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
        $_ENV['PROFILER_ENABLED'] = false;
        $this->app = $this->app();
        $timerA = $this->app->make(Timer::class);
        $timerB = $this->app->make(Timer::class);

        $this->assertInstanceOf(NullTimerService::class, $timerA);
        $this->assertSame($timerA, $timerB);
    }

    /** @test */
    function memory_is_singleton()
    {
        $memoryA = $this->app->make(Memory::class);
        $memoryB = $this->app->make(Memory::class);

        $this->assertInstanceOf(Memory::class, $memoryA);
        $this->assertSame($memoryA, $memoryB);
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        putenv('PROFILER_ENABLED');
        unset($_ENV['PROFILER_ENABLED']);
    }
}
