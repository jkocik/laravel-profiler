<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\BindingsTracker;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyClassA;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyClassB;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyContractA;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyContractB;

class BindingsTrackerTest extends TestCase
{
    /** @test */
    function has_container_bindings_abstracts()
    {
        $tracker = $this->app->make(BindingsTracker::class);

        $tracker->terminate();
        $bindings = $tracker->data()->get('bindings');

        $this->assertNotNull($bindings);
        $this->assertCount(count($bindings), $this->app->getBindings());
        $this->assertSame(0, $bindings->keys()[0]);
        $this->assertContains($bindings->first()['abstract'], array_keys($this->app->getBindings()));
    }

    /** @test */
    function tracks_how_container_resolves_bindings()
    {
        $this->app->bind(DummyContractA::class, DummyClassA::class);
        $this->app->bind(DummyContractB::class, DummyClassB::class);
        $this->app->make(DummyContractB::class);
        $tracker = $this->app->make(BindingsTracker::class);

        $tracker->terminate();
        $bindings = $tracker->data()->get('bindings');

        $this->assertNull($bindings->where('abstract', DummyContractA::class)->first()['resolved']);
        $this->assertEquals(DummyClassB::class, $bindings->where('abstract', DummyContractB::class)->first()['resolved']);
    }
}
