<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\ViewsTracker;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\ProcessorA;

class TrackersResetTest extends TestCase
{
    /** @test */
    function trackers_can_be_reset()
    {
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.trackers', [
                ViewsTracker::class,
            ]);
            $app->make('config')->set('profiler.processors', [
                ProcessorA::class,
            ]);
            $app->singleton(ProcessorA::class, function () {
                return new ProcessorA();
            });
            $app['view']->addNamespace('tests', __DIR__ . '/../Support/Fixtures');
        });

        view('tests::dummy-view-a')->render();

        profiler_reset();

        view('tests::dummy-view-b')->render();

        $this->app->terminate();
        $processorA = $this->app->make(ProcessorA::class);

        $this->assertCount(1, $processorA->data->get('views'));
        $this->assertEquals('tests::dummy-view-b', $processorA->data->get('views')->first()['name']);
    }

    /** @test */
    function profiler_reset_function_can_be_executed_even_profiler_is_disabled()
    {
        $this->app = $this->appWith(function (Application $app) {
            $app->make('config')->set('profiler.enabled', false);
        });

        profiler_reset();

        $this->assertTrue(true);
    }
}
