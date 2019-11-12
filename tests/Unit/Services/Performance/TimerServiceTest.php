<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Services\Performance;

use Mockery;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\Timer;
use JKocik\Laravel\Profiler\Services\Performance\TimerService;
use JKocik\Laravel\Profiler\Services\Performance\TimerException;
use JKocik\Laravel\Profiler\Services\Performance\NullTimerService;

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
        $appL = Mockery::mock(Application::class);
        $appL->shouldReceive('environment')->with('testing')->andReturn(false)->once();

        $appT = Mockery::mock(Application::class);
        $appT->shouldReceive('environment')->with('testing')->andReturn(true)->once();

        $timerA1 = new TimerService($appL);
        $timerB1 = new TimerService($appT);
        $timerA2 = new TimerService($appL);
        $timerB2 = new TimerService($appT);
        $millisecondsWithLaravelStartDefined = \microtime(true) * 1000;

        $timerA1->startLaravel();
        $timerA1->finishLaravel();
        $timerB1->startLaravel();
        $timerB1->finishLaravel();

        define('LARAVEL_START', 0);
        $timerA2->startLaravel();
        $timerA2->finishLaravel();
        $timerB2->startLaravel();
        $timerB2->finishLaravel();

        $this->assertLessThan($millisecondsWithLaravelStartDefined, $timerA1->milliseconds('laravel'));
        $this->assertGreaterThanOrEqual($millisecondsWithLaravelStartDefined, $timerA2->milliseconds('laravel'));
        $this->assertLessThan($millisecondsWithLaravelStartDefined, $timerB1->milliseconds('laravel'));
        $this->assertLessThan($millisecondsWithLaravelStartDefined, $timerB2->milliseconds('laravel'));
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

        $this->assertEquals(-1, $timer->milliseconds('testA'));
    }

    /** @test */
    function allows_custom_timer_for_application_developers_but_keep_it_in_different_namespace()
    {
        $timer = $this->app->make(TimerService::class);

        $timer->startCustom('testA');
        $timer->finishCustom('testA');

        $this->assertGreaterThan(0, $timer->millisecondsCustom('testA'));
        $this->assertNotEquals($timer->milliseconds('testA'), $timer->millisecondsCustom('testA'));
    }

    /** @test */
    function returns_empty_values_for_null_timer()
    {
        $timer = new NullTimerService();

        $timer->startCustom('testA');
        $timer->finishCustom('testA');

        $this->assertEquals(-1, $timer->millisecondsCustom('testA'));
    }

    /** @test */
    function the_same_custom_timer_can_not_be_started_more_than_once()
    {
        try {
            $timer = $this->app->make(TimerService::class);

            $timer->startCustom('testA');
            $timer->startCustom('testA');
        } catch (TimerException $e) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('TimerException should be thrown');
    }

    /** @test */
    function the_same_custom_timer_can_not_be_finished_more_than_once()
    {
        try {
            $timer = $this->app->make(TimerService::class);

            $timer->startCustom('testA');
            $timer->finishCustom('testA');
            $timer->finishCustom('testA');
        } catch (TimerException $e) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('TimerException should be thrown');
    }

    /** @test */
    function custom_timer_can_not_be_finished_if_is_not_started_before()
    {
        try {
            $timer = $this->app->make(TimerService::class);

            $timer->finishCustom('testA');
        } catch (TimerException $e) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('TimerException should be thrown');
    }

    /** @test */
    function custom_timer_is_used_by_helper_functions()
    {
        profiler_start('testA');
        profiler_finish('testA');

        $timer = $this->app->make(Timer::class);

        $this->assertGreaterThan(0, $timer->millisecondsCustom('testA'));
    }

    /** @test */
    function custom_timer_functions_exceptions_are_caught_and_logged_if_configured()
    {
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.handle_exceptions', 1);
        });

        Log::shouldReceive('error')
            ->times(2)
            ->with(TimerException::class);

        profiler_start('testA');
        profiler_start('testA');
        profiler_finish('testB');
    }

    /** @test */
    function custom_timer_functions_exceptions_are_thrown_if_configured()
    {
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.handle_exceptions', 666);
        });

        Log::shouldReceive('error')
            ->times(0)
            ->with(TimerException::class);

        try {
            profiler_start('testA');
            profiler_start('testA');
        } catch (TimerException $e) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('TimerException should be thrown');
    }

    /** @test */
    function custom_timer_functions_exceptions_are_caught_and_not_logged_if_configured()
    {
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.handle_exceptions', 0);
        });

        Log::shouldReceive('error')
            ->times(0);

        profiler_start('testA');
        profiler_start('testA');
        profiler_finish('testB');
    }

    /** @test */
    function custom_timer_functions_exceptions_are_caught_and_not_logged_if_configured_incorrectly()
    {
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.handle_exceptions', -1);
        });

        Log::shouldReceive('error')
            ->times(0);

        profiler_start('testA');
        profiler_start('testA');
        profiler_finish('testB');
    }
}
