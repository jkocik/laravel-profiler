<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\Timer;
use JKocik\Laravel\Profiler\Tests\Support\PHPMock;
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
    function has_laravel_total_execution_time()
    {
        $this->app->terminate();
        $timer = $this->app->make(Timer::class);
        $processor = $this->app->make(PerformanceProcessor::class);

        $this->assertTrue($processor->performance->has('timer'));
        $this->assertGreaterThan(0, $timer->milliseconds('laravel'));
        $this->assertEquals($timer->milliseconds('laravel'), $processor->performance->get('timer')['laravel']);
    }

    /** @test */
    function has_booting_time()
    {
        $this->app->terminate();
        $timer = $this->app->make(Timer::class);
        $processor = $this->app->make(PerformanceProcessor::class);

        $this->assertGreaterThan(0, $timer->milliseconds('boot'));
        $this->assertEquals($timer->milliseconds('boot'), $processor->performance->get('timer')['boot']);
    }

    /** @test */
    function has_middleware_time()
    {
        $this->get('/');
        $timer = $this->app->make(Timer::class);
        $processor = $this->app->make(PerformanceProcessor::class);

        $this->assertGreaterThan(0, $timer->milliseconds('middleware'));
        $this->assertEquals($timer->milliseconds('middleware'), $processor->performance->get('timer')['middleware']);
    }

    /** @test */
    function has_handle_request_time()
    {
        $this->get('/');
        $timer = $this->app->make(Timer::class);
        $processor = $this->app->make(PerformanceProcessor::class);

        $this->assertGreaterThan(0, $timer->milliseconds('request'));
        $this->assertEquals($timer->milliseconds('request'), $processor->performance->get('timer')['request']);
    }

    /** @test */
    function has_send_response_and_terminate_time()
    {
        $this->get('/');
        $timer = $this->app->make(Timer::class);
        $processor = $this->app->make(PerformanceProcessor::class);

        $this->assertGreaterThan(0, $timer->milliseconds('response'));
        $this->assertEquals($timer->milliseconds('response'), $processor->performance->get('timer')['response']);
    }

    /** @test */
    function has_memory_peak()
    {
        $this->app->terminate();
        $processor = $this->app->make(PerformanceProcessor::class);

        $this->assertEquals(PHPMock::MEMORY_USAGE, $processor->performance->get('memory')['peak']);
    }
}
