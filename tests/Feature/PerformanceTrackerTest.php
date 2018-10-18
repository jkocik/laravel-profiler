<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\Timer;
use JKocik\Laravel\Profiler\Tests\Support\PHPMock;
use Illuminate\Foundation\Bootstrap\BootProviders;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\PerformanceProcessor;

class PerformanceTrackerTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.processors', [
                PerformanceProcessor::class,
            ]);
            $app->singleton(PerformanceProcessor::class, function () {
                return new PerformanceProcessor();
            });
        });
    }

    /** @test */
    function has_timer_laravel()
    {
        $this->app->terminate();
        $timer = $this->app->make(Timer::class);
        $processor = $this->app->make(PerformanceProcessor::class);

        $this->assertTrue($processor->performance->has('timer'));
        $this->assertGreaterThan(0, $timer->milliseconds('laravel'));
        $this->assertEquals($timer->milliseconds('laravel'), $processor->performance->get('timer')['laravel']);
    }

    /** @test */
    function has_timer_bootstrap()
    {
        event('bootstrapping: ' . BootProviders::class, [$this]);
        event('bootstrapped: ' . BootProviders::class, [$this]);
        $this->app->terminate();
        $timer = $this->app->make(Timer::class);
        $processor = $this->app->make(PerformanceProcessor::class);

        $this->assertGreaterThan(0, $timer->milliseconds('bootstrap'));
        $this->assertEquals($timer->milliseconds('bootstrap'), $processor->performance->get('timer')['bootstrap']);
    }

    /** @test */
    function has_memory_peak()
    {
        $this->app->terminate();
        $processor = $this->app->make(PerformanceProcessor::class);

        $this->assertEquals(PHPMock::MEMORY_USAGE, $processor->performance->get('memory')['peak']);
    }
}
