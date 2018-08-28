<?php

namespace JKocik\Laravel\Profiler\Tests\Unit;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\QueriesTracker;

class QueriesTrackerTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
    }

    /**
     * @return void
     */
    protected function tearDown()
    {
        parent::tearDown();

        Artisan::call('migrate:rollback');
    }

    /** @test */
    function has_executed_queries()
    {
        $tracker = $this->app->make(QueriesTracker::class);
        $user = factory(User::class)->create();

        $tracker->terminate();
        $queries = $tracker->data()->get('queries');

        $this->assertNotNull($queries);
        $this->assertContains('insert into `users` (`name`, `email`, `password`', $queries->first()['sql']);
        $this->assertContains($user->email, $queries->first()['bindings']);
        $this->assertArrayHasKey('time', $queries->first());
        $this->assertEquals(':memory:', $queries->first()['database']);
        $this->assertEquals('sqlite', $queries->first()['name']);
        $this->assertContains('insert into `users` (`name`, `email`, `password`', $queries->first()['query']);
        $this->assertContains("values ('{$user->name}', '{$user->email}", $queries->first()['query']);
    }

    /** @test */
    function has_query_bindings_for_int_and_float_values()
    {
        $tracker = $this->app->make(QueriesTracker::class);
        User::whereEmail(1)->first();
        User::whereEmail(1.1)->first();
        User::whereEmail('1')->first();
        User::whereEmail('1.1')->first();

        $tracker->terminate();
        $queries = $tracker->data()->get('queries');

        $this->assertContains("where `email` = 1", $queries->shift()['query']);
        $this->assertContains("where `email` = 1.1", $queries->shift()['query']);
        $this->assertContains("where `email` = '1'", $queries->shift()['query']);
        $this->assertContains("where `email` = '1.1'", $queries->shift()['query']);
    }

    /** @test */
    function has_query_bindings_for_null_values()
    {
        $tracker = $this->app->make(QueriesTracker::class);
        User::whereNull('email')->first();
        User::whereNotNull('email')->first();

        $tracker->terminate();
        $queries = $tracker->data()->get('queries');

        $this->assertContains("where `email` is null", $queries->shift()['query']);
        $this->assertContains("where `email` is not null", $queries->shift()['query']);
    }

    /** @test */
    function has_query_bindings_names_as_string()
    {
        $tracker = $this->app->make(QueriesTracker::class);
        DB::select('select * from users where email = :email and name = :name', ['email' => 'abc@example.com', 'name' => 1]);

        $tracker->terminate();
        $queries = $tracker->data()->get('queries');

        $this->assertContains("where email = 'abc@example.com' and name = 1", $queries->first()['query']);
    }

    /** @test */
    function has_query_bindings_objects()
    {
        $tracker = $this->app->make(QueriesTracker::class);
        DB::select('select * from users where email = :email', ['email' => new \DateTime()]);

        $tracker->terminate();
        $queries = $tracker->data()->get('queries');

        $this->assertContains("where email = {object}", $queries->first()['query']);
    }
}
