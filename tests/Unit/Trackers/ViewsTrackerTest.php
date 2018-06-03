<?php

namespace JKocik\Laravel\Profiler\Tests\Unit;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\ViewsTracker;

class ViewsTrackerTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->app['view']->addNamespace('tests', __DIR__ . '/../../Support/Fixtures');
    }

    /** @test */
    function has_views()
    {
        $tracker = $this->app->make(ViewsTracker::class);

        view('tests::dummy-view-a')->render();
        view('tests::dummy-view-b')->render();

        $tracker->terminate();
        $views = $tracker->data()->get('views');

        $this->assertNotNull($views);
        $this->assertCount(2, $views);
    }

    /** @test */
    function has_view_name()
    {
        $tracker = $this->app->make(ViewsTracker::class);

        view('tests::dummy-view-a')->render();

        $tracker->terminate();
        $views = $tracker->data()->get('views');

        $this->assertEquals('tests::dummy-view-a', $views->first()['name']);
    }

    /** @test */
    function has_view_path()
    {
        $tracker = $this->app->make(ViewsTracker::class);

        view('tests::dummy-view-a')->render();

        $tracker->terminate();
        $views = $tracker->data()->get('views');

        $this->assertEquals(__DIR__ . '/../../Support/Fixtures/dummy-view-a.blade.php', $views->first()['path']);
    }

    /** @test */
    function has_view_data()
    {
        $tracker = $this->app->make(ViewsTracker::class);

        $user = ['name' => 'Joe'];
        view('tests::dummy-view-a', compact('user'))->render();

        $tracker->terminate();
        $views = $tracker->data()->get('views');

        $this->assertEquals(['name' => 'Joe'], $views->first()['data']['user']);
    }
}
