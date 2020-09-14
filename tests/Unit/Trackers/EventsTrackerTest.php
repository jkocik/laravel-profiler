<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use Exception;
use Illuminate\Events\Dispatcher;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Events\Tracking;
use JKocik\Laravel\Profiler\Events\Terminating;
use JKocik\Laravel\Profiler\Events\ProfilerBound;
use JKocik\Laravel\Profiler\Events\ResetTrackers;
use JKocik\Laravel\Profiler\Trackers\EventsTracker;
use JKocik\Laravel\Profiler\Events\ExceptionHandling;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyClassA;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyClassB;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyEventA;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyEventB;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyListenerA;

class EventsTrackerTest extends TestCase
{
    /** @test */
    function has_fired_events()
    {
        $tracker = $this->app->make(EventsTracker::class);
        $dispatcher = $this->app->make(Dispatcher::class);

        $this->tapLaravelVersionTill(5.3, function () use ($dispatcher) {
            $dispatcher->fire('testing: tracker', [new \stdClass()]);
        });
        $this->tapLaravelVersionFrom(5.4, function () use ($dispatcher) {
            $dispatcher->dispatch('testing: tracker', [new \stdClass()]);
        });
        event(new DummyEventA());

        $tracker->terminate();
        $events = $tracker->data()->get('events');

        $this->assertNotNull($events);
        $this->assertTrue($events->contains('name', 'testing: tracker'));
        $this->assertTrue($events->contains('name', DummyEventA::class));
        $this->assertEquals(2, $tracker->meta()->get('events_count'));
    }

    /** @test */
    function has_data_of_fired_events()
    {
        $this->app->make('config')->set('profiler.data.events', true);
        $this->app->make('config')->set('profiler.group.events', false);

        $tracker = $this->app->make(EventsTracker::class);

        $user = $this->user(['email' => 'a@example.com']);
        $usersA = collect([$this->user(['email' => 'b@example.com']), $this->user(['email' => 'c@example.com'])]);
        $usersB = [$this->user(['email' => 'd@example.com']), $this->user(['email' => collect(['email' => 'e@example.com'])])];
        $dummyClasses = [new DummyClassA(), new DummyClassB()];
        $dataA = ['a' => 1, 'c' => 2];
        $dataB = 'c';

        event(new DummyEventB($user, $usersA, $usersB, $dummyClasses, $dataA, $dataB));

        $tracker->terminate();
        $events = $tracker->data()->get('events');
        $eventB = $events->where('name', DummyEventB::class)->first();

        $this->assertEquals(['email' => 'a@example.com'], $eventB['data']['user']);
        $this->assertEquals([
            0 => ['email' => 'b@example.com'],
            1 => ['email' => 'c@example.com'],
        ], $eventB['data']['usersA']);
        $this->assertEquals([
            0 => ['email' => 'd@example.com'],
            1 => ['email' => ['email' => 'e@example.com']],
        ], $eventB['data']['usersB']);
        $this->assertEquals([DummyClassA::class, DummyClassB::class], $eventB['data']['dummyClasses']);
        $this->assertEquals(['a' => 1, 'c' => 2], $eventB['data']['dataA']);
        $this->assertEquals('c', $eventB['data']['dataB']);
    }

    /** @test */
    function has_not_data_of_fired_events_if_data_tracking_is_disabled_in_config()
    {
        $this->app->make('config')->set('profiler.group.events', false);

        $tracker = $this->app->make(EventsTracker::class);

        $user = $this->user(['email' => 'a@example.com']);
        $usersA = collect([$this->user(['email' => 'b@example.com']), $this->user(['email' => 'c@example.com'])]);
        $usersB = [$this->user(['email' => 'd@example.com']), $this->user(['email' => 'e@example.com'])];
        $dummyClasses = [new DummyClassA(), new DummyClassB()];
        $dataA = ['a' => 1, 'c' => 2];
        $dataB = 'c';

        event(new DummyEventB($user, $usersA, $usersB, $dummyClasses, $dataA, $dataB));

        $tracker->terminate();
        $events = $tracker->data()->get('events');
        $eventB = $events->where('name', DummyEventB::class)->first();

        $this->assertArrayNotHasKey('data', $eventB);
    }

    /** @test */
    function can_group_events_with_the_same_name()
    {
        $this->app->make('config')->set('profiler.data.events', true);

        $tracker = $this->app->make(EventsTracker::class);
        $dispatcher = $this->app->make(Dispatcher::class);

        $this->tapLaravelVersionTill(5.3, function () use ($dispatcher) {
            $dispatcher->fire('testing: eventA', [new \stdClass()]);
            $dispatcher->fire('testing: eventB', [new \stdClass()]);
            $dispatcher->fire('testing: eventB', [new \stdClass()]);
            $dispatcher->fire('testing: eventC', [new \stdClass()]);
            $dispatcher->fire('testing: eventC', [new \stdClass()]);
            $dispatcher->fire('testing: eventC', [new \stdClass()]);
            $dispatcher->fire('testing: eventC', [new \stdClass()]);
            $dispatcher->fire('testing: eventD', [new \stdClass()]);
        });
        $this->tapLaravelVersionFrom(5.4, function () use ($dispatcher) {
            $dispatcher->dispatch('testing: eventA', [new \stdClass()]);
            $dispatcher->dispatch('testing: eventB', [new \stdClass()]);
            $dispatcher->dispatch('testing: eventB', [new \stdClass()]);
            $dispatcher->dispatch('testing: eventC', [new \stdClass()]);
            $dispatcher->dispatch('testing: eventC', [new \stdClass()]);
            $dispatcher->dispatch('testing: eventC', [new \stdClass()]);
            $dispatcher->dispatch('testing: eventC', [new \stdClass()]);
            $dispatcher->dispatch('testing: eventD', [new \stdClass()]);
        });

        $tracker->terminate();
        $events = $tracker->data()->get('events');

        $this->assertEquals([
            'name' => 'testing: eventA',
            'count' => 1,
            'data' => collect([]),
        ], $events->pull(0));
        $this->assertEquals([
            'name' => 'testing: eventB',
            'count' => 2,
        ], $events->pull(1));
        $this->assertEquals([
            'name' => 'testing: eventC',
            'count' => 4,
        ], $events->pull(2));
        $this->assertEquals([
            'name' => 'testing: eventD',
            'count' => 1,
            'data' => collect([]),
        ], $events->pull(3));
        $this->assertEquals(8, $tracker->meta()->get('events_count'));
    }

    /** @test */
    function does_not_group_events_if_events_group_is_disabled_in_config()
    {
        $this->app->make('config')->set('profiler.data.events', true);
        $this->app->make('config')->set('profiler.group.events', false);

        $tracker = $this->app->make(EventsTracker::class);
        $dispatcher = $this->app->make(Dispatcher::class);

        $this->tapLaravelVersionTill(5.3, function () use ($dispatcher) {
            $dispatcher->fire('testing: eventA', [new \stdClass()]);
            $dispatcher->fire('testing: eventB', [new \stdClass()]);
            $dispatcher->fire('testing: eventB', [new \stdClass()]);
        });
        $this->tapLaravelVersionFrom(5.4, function () use ($dispatcher) {
            $dispatcher->dispatch('testing: eventA', [new \stdClass()]);
            $dispatcher->dispatch('testing: eventB', [new \stdClass()]);
            $dispatcher->dispatch('testing: eventB', [new \stdClass()]);
        });

        $tracker->terminate();
        $events = $tracker->data()->get('events');

        $this->assertEquals([
            'name' => 'testing: eventA',
            'count' => 1,
            'data' => collect([]),
        ], $events->pull(0));
        $this->assertEquals([
            'name' => 'testing: eventB',
            'count' => 1,
            'data' => collect([]),
        ], $events->pull(1));
        $this->assertEquals([
            'name' => 'testing: eventB',
            'count' => 1,
            'data' => collect([]),
        ], $events->pull(2));
        $this->assertEquals(3, $tracker->meta()->get('events_count'));
    }

    /** @test */
    function does_not_track_laravel_profiler_internal_events()
    {
        $tracker = $this->app->make(EventsTracker::class);

        event(new ExceptionHandling(new Exception()));
        event(new ProfilerBound());
        event(new ResetTrackers());
        event(new Terminating());
        event(new Tracking());

        $tracker->terminate();

        $this->assertEquals(0, $tracker->meta()->get('events_count'));
    }

    /** @test */
    function does_not_track_laravel_framework_events()
    {
        $tracker = $this->app->make(EventsTracker::class);

        event('bootstrapped: ' . \Illuminate\Foundation\Bootstrap\BootProviders::class, [new \stdClass()]);

        $tracker->terminate();

        $this->assertEquals(0, $tracker->meta()->get('events_count'));
    }

    /** @test */
    function can_reset_events()
    {
        $tracker = $this->app->make(EventsTracker::class);
        event(new DummyEventA());
        event(new DummyEventA());
        event(new DummyEventA());

        profiler_reset();

        event(new DummyEventA());

        $tracker->terminate();

        $this->assertEquals(1, $tracker->meta()->get('events_count'));
        $this->assertCount(1, $tracker->data()->get('events'));
    }
}
