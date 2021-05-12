<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\ViewsTracker;

class ViewsTrackerTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
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
        $this->app->make('config')->set('profiler.data.views', true);

        $tracker = $this->app->make(ViewsTracker::class);

        $user = ['name' => 'Joe'];
        view('tests::dummy-view-a', compact('user'))->render();

        $tracker->terminate();
        $views = $tracker->data()->get('views');

        $this->assertEquals(['name' => 'Joe'], $views->first()['data']['user']);
        $this->assertArrayNotHasKey('params', $views->first());
    }

    /** @test */
    function has_not_data_of_views_if_data_tracking_is_disabled_in_config()
    {
        $tracker = $this->app->make(ViewsTracker::class);

        $user = ['name' => 'Joe'];
        view('tests::dummy-view-a', compact('user'))->render();

        $tracker->terminate();
        $views = $tracker->data()->get('views');

        $this->assertArrayNotHasKey('data', $views->first());
    }

    /** @test */
    function has_view_params_if_data_tracking_is_disabled_in_config()
    {
        $tracker = $this->app->make(ViewsTracker::class);

        $name = 'Joe';
        $visits = 125;
        $price = 5.89;
        $active = true;
        $address = null;
        $related = $this->user(['email' => 'a@example.com']);
        $roles = collect(['publisher', 'viewer']);
        $tags = ['a'];
        $comments = [];
        view('tests::dummy-view-a', compact(
            'name',
            'visits',
            'price',
            'active',
            'address',
            'related',
            'roles',
            'tags',
            'comments'
        ))->render();

        $tracker->terminate();
        $views = $tracker->data()->get('views');

        $this->assertArrayNotHasKey('data', $views->first());
        $this->assertEquals([
            'name' => 'string',
            'visits' => 'integer',
            'price' => 'double',
            'active' => 'boolean',
            'address' => 'NULL',
            'related' => $this->userClass(),
            'roles' => 'Illuminate\Support\Collection: 2 item(s)',
            'tags' => 'array: 1 item(s)',
            'comments' => 'array: 0 item(s)',
        ], $views->first()['params']);
    }

    /** @test */
    function can_reset_views()
    {
        $tracker = $this->app->make(ViewsTracker::class);
        view('tests::dummy-view-a')->render();
        view('tests::dummy-view-a')->render();
        view('tests::dummy-view-a')->render();

        profiler_reset();

        view('tests::dummy-view-a')->render();

        $tracker->terminate();

        $this->assertCount(1, $tracker->data()->get('views'));
    }
}
