<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use Exception;
use Carbon\Carbon;
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
        $this->factoryUser()->create([
            'name' => 'Joe',
            'email' => 'joe@example.com',
        ]);

        $tracker->terminate();
        $queries = $tracker->data()->get('queries');

        $this->assertNotNull($queries);
        $this->assertContains('query', $queries->first()['type']);
        $this->assertContains('insert into `users` (`name`, `email`', $queries->first()['sql']);
        $this->assertContains('joe@example.com', $queries->first()['bindings']);
        $this->assertArrayHasKey('time', $queries->first());
        $this->assertEquals(':memory:', $queries->first()['database']);
        $this->assertEquals('sqlite', $queries->first()['name']);
        $this->assertContains('insert into `users` (`name`, `email`', $queries->first()['query']);
        $this->assertContains("values ('Joe', 'joe@example.com'", $queries->first()['query']);
    }

    /** @test */
    function has_committed_transactions()
    {
        $tracker = $this->app->make(QueriesTracker::class);
        DB::transaction(function () {
            $this->factoryUser()->create();
        });

        $tracker->terminate();
        $queries = $tracker->data()->get('queries');

        $this->assertContains('transaction-begin', $queries->first()['type']);
        $this->assertEquals(':memory:', $queries->first()['database']);
        $this->assertEquals('sqlite', $queries->first()['name']);

        $this->assertContains('transaction-commit', $queries->last()['type']);
        $this->assertEquals(':memory:', $queries->last()['database']);
        $this->assertEquals('sqlite', $queries->last()['name']);
    }

    /** @test */
    function has_rolled_back_transactions()
    {
        $tracker = $this->app->make(QueriesTracker::class);
        try {
            DB::transaction(function () {
                throw new Exception();
            });
        } catch (Exception $e) {}

        $tracker->terminate();
        $queries = $tracker->data()->get('queries');

        $this->assertContains('transaction-rollback', $queries->last()['type']);
        $this->assertEquals(':memory:', $queries->last()['database']);
        $this->assertEquals('sqlite', $queries->last()['name']);
    }

    /** @test */
    function can_count_queries()
    {
        $tracker = $this->app->make(QueriesTracker::class);
        DB::select('select * from users');
        $this->factoryUser()->create();

        $tracker->terminate();

        $this->assertEquals(2, $tracker->meta()->get('queries_count'));
    }

    /** @test */
    function can_count_queries_without_transactions()
    {
        $tracker = $this->app->make(QueriesTracker::class);
        DB::transaction(function () {
            DB::select('select * from users');
            $this->factoryUser()->create();
        });

        $tracker->terminate();

        $this->assertEquals(2, $tracker->meta()->get('queries_count'));
    }

    /** @test */
    function has_bindings_keys_names_if_specified()
    {
        $tracker = $this->app->make(QueriesTracker::class);
        DB::select('select * from users where email = :email and name = :name', ['email' => 'abc@example.com', 'name' => 1]);

        $tracker->terminate();
        $queries = $tracker->data()->get('queries');

        $this->assertArrayHasKey('email', $queries->first()['bindings']);
        $this->assertArrayHasKey('name', $queries->first()['bindings']);
    }

    /** @test */
    function has_query_bindings_for_int_and_float_values()
    {
        $tracker = $this->app->make(QueriesTracker::class);
        $this->userClass()::whereEmail(1)->first();
        $this->userClass()::whereEmail(1.1)->first();
        $this->userClass()::whereEmail('1')->first();
        $this->userClass()::whereEmail('1.1')->first();

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
        $this->userClass()::whereNull('email')->first();
        $this->userClass()::whereNotNull('email')->first();

        $tracker->terminate();
        $queries = $tracker->data()->get('queries');

        $this->assertContains("where `email` is null", $queries->shift()['query']);
        $this->assertContains("where `email` is not null", $queries->shift()['query']);
    }

    /** @test */
    function has_query_bindings_for_bool_values()
    {
        $tracker = $this->app->make(QueriesTracker::class);
        DB::select('select * from users where 1 = :first and 0 = :second', ['first' => true, 'second' => false]);

        $tracker->terminate();
        $queries = $tracker->data()->get('queries');

        $this->tapLaravelVersionFrom(5.5, function () use ($queries) {
            $this->assertContains("where 1 = 1 and 0 = 0", $queries->first()['query']);
            $this->assertSame(1, $queries->first()['bindings']['first']);
            $this->assertSame(0, $queries->first()['bindings']['second']);
        });

        $this->tapLaravelVersionTill(5.4, function () use ($queries) {
            $this->assertContains("where 1 = '1' and 0 = 0", $queries->first()['query']);
            $this->assertSame(true, $queries->first()['bindings']['first']);
            $this->assertSame(0, $queries->first()['bindings']['second']);
        });
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
        $dateTime = Carbon::now();

        $tracker = $this->app->make(QueriesTracker::class);
        DB::select('select * from users where email = :email', ['email' => $dateTime]);

        $tracker->terminate();
        $queries = $tracker->data()->get('queries');

        $this->assertContains("where email = '{$dateTime->toDateTimeString()}'", $queries->first()['query']);
    }

    /** @test */
    function has_query_bindings_truncated_if_binding_string_is_long()
    {
        $tracker = $this->app->make(QueriesTracker::class);
        DB::select('select * from users where email = :email', ['email' => str_repeat('a', 255)]);
        DB::select('select * from users where email = :email', ['email' => str_repeat('a', 256)]);

        $tracker->terminate();
        $queries = $tracker->data()->get('queries');

        $this->assertContains("where email = '". str_repeat('a', 255) ."'", $queries->first()['query']);
        $this->assertContains(str_repeat('a', 255), $queries->first()['bindings']);
        $this->assertContains("where email = '". str_repeat('a', 255) ."...{truncated}'", $queries->last()['query']);
        $this->assertContains(str_repeat('a', 255) . '...{truncated}', $queries->last()['bindings']);
    }

    /** @test */
    function can_reset_queries()
    {
        $tracker = $this->app->make(QueriesTracker::class);
        DB::select('select * from users');
        DB::select('select * from users');
        DB::select('select * from users');

        profiler_reset();

        DB::select('select * from users');

        $tracker->terminate();

        $this->assertEquals(1, $tracker->meta()->get('queries_count'));
        $this->assertCount(1, $tracker->data()->get('queries'));
    }
}
