<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\AuthTracker;

class AuthTrackerTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate');
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        Artisan::call('migrate:rollback');
    }

    /** @test */
    function has_auth_user()
    {
        $tracker = $this->app->make(AuthTracker::class);

        $user = $this->factoryUser()->create(['email' => 'user@example.com']);
        Auth::login($user);

        $tracker->terminate();
        $auth = $tracker->data()->get('auth');

        $this->assertEquals($user->email, $auth->email);
    }

    /** @test */
    function has_auth_user_even_user_is_logging_out()
    {
        $tracker = $this->app->make(AuthTracker::class);

        $user = $this->factoryUser()->create([
            'email' => 'login.me@example.com',
        ]);
        Auth::login($user);
        Auth::logout();

        $tracker->terminate();
        $auth = $tracker->data()->get('auth');

        $this->assertEquals($user->email, $auth->email);
    }

    /** @test */
    function has_null_auth_user_if_user_is_not_logged_in()
    {
        $tracker = $this->app->make(AuthTracker::class);

        $tracker->terminate();
        $auth = $tracker->data()->get('auth');

        $this->assertNull($auth);
    }
}
