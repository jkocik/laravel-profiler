<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Services\Timer;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Services\Timer\TimerService;

class TimerServiceTest extends TestCase
{
    /** @test */
    function counts_execution_time_in_milliseconds()
    {
        $timer = $this->app->make(TimerService::class);

        $timer->start('testA');
        $timer->finish('testA');

        $timer->start('testB');
        usleep(10 * 1000);
        $timer->finish('testB');

        $timer->start('testC');
        usleep(200 * 1000);
        $timer->finish('testC');

        $this->assertGreaterThanOrEqual(0, $timer->milliseconds('testA'));
        $this->assertGreaterThanOrEqual(10, $timer->milliseconds('testB'));
        $this->assertGreaterThanOrEqual(200, $timer->milliseconds('testC'));
    }

    /** @test */
    function counts_laravel_execution_time()
    {
        $timerA = $this->app->make(TimerService::class);
        $timerB = $this->app->make(TimerService::class);
        $millisecondsWithLaravelStartDefined = \microtime(true) * 1000;

        $timerA->startLaravel();
        $timerA->finishLaravel();

        define('LARAVEL_START', 0);
        $timerB->startLaravel();
        $timerB->finishLaravel();

        $this->assertLessThan($millisecondsWithLaravelStartDefined, $timerA->milliseconds('laravel'));
        $this->assertGreaterThanOrEqual($millisecondsWithLaravelStartDefined, $timerB->milliseconds('laravel'));
    }

    /** @test */
    function returns_all_finished_times()
    {
        $timer = $this->app->make(TimerService::class);

        $timer->start('testA');
        $timer->finish('testA');

        $timer->start('testB');
        $timer->finish('testB');

        $timer->start('testC');

        $this->assertArrayHasKey('testA', $timer->all());
        $this->assertArrayHasKey('testB', $timer->all());
        $this->assertArrayNotHasKey('testC', $timer->all());
        $this->assertEquals($timer->milliseconds('testA'), $timer->all()['testA']);
        $this->assertEquals($timer->milliseconds('testB'), $timer->all()['testB']);
    }

    /** @test */
    function returns_negative_value_when_timer_for_specific_label_is_not_completed()
    {
        $timer = $this->app->make(TimerService::class);

        $timer->start('testA');
        $timer->finish('testB');

        $this->assertEquals(-1, $timer->milliseconds('testA'));
        $this->assertEquals(-1, $timer->milliseconds('testB'));
    }
}
