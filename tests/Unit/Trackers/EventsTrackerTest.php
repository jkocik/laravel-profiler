<?php

namespace JKocik\Laravel\Profiler\Tests\Unit;

use App\User;
use Illuminate\Events\Dispatcher;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\EventsTracker;
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

        $dispatcher->fire('testing: tracker', [new \stdClass()]);
        event(new DummyEventA());

        $tracker->terminate();
        $events = $tracker->data()->get('events');

        $this->assertNotNull($events);
        $this->assertTrue($events->contains('name', 'testing: tracker'));
        $this->assertTrue($events->contains('name', DummyEventA::class));
    }

    /** @test */
    function has_data_of_fired_events()
    {
        $tracker = $this->app->make(EventsTracker::class);

        $user = new User(['email' => 'a@example.com']);
        $usersA = collect([new User(['email' => 'b@example.com']), new User(['email' => 'c@example.com'])]);
        $usersB = [new User(['email' => 'd@example.com']), new User(['email' => 'e@example.com'])];
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
            1 => ['email' => 'e@example.com'],
        ], $eventB['data']['usersB']);
        $this->assertEquals([DummyClassA::class, DummyClassB::class], $eventB['data']['dummyClasses']);
        $this->assertEquals(['a' => 1, 'c' => 2], $eventB['data']['dataA']);
        $this->assertEquals('c', $eventB['data']['dataB']);
    }
}
