<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use Mockery;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Events\ExceptionHandling;
use JKocik\Laravel\Profiler\Trackers\ExceptionTracker;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyException;

class ExceptionTrackerTest extends TestCase
{
    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->turnOffProcessors();
    }

    /** @test */
    function has_exception()
    {
        Event::listen(ExceptionHandling::class, function (ExceptionHandling $exceptionHandling) {
            $this->exception = $exceptionHandling->exception;
        });

        $tracker = $this->app->make(ExceptionTracker::class);

        $this->get('/i-can-not-find-that-page');

        $tracker->terminate();
        $exception = $tracker->data()->get('exception');

        $this->assertSame($this->exception->getMessage(), $exception->get('message'));
        $this->assertSame(NotFoundHttpException::class, $exception->get('exception'));
        $this->assertSame($this->exception->getFile(), $exception->get('file'));
        $this->assertSame($this->exception->getLine(), $exception->get('line'));
        $this->assertEquals(collect($this->exception->getTrace())->map(function ($trace) {
            return Arr::except($trace, ['args', 'type']);
        }), $exception->get('trace'));
    }

    /** @test */
    function has_not_exception_on_correct_framework_execution()
    {
        $tracker = $this->app->make(ExceptionTracker::class);

        $this->get('/');

        $tracker->terminate();
        $exception = $tracker->data()->get('exception');

        $this->assertNull($exception);
    }

    /** @test */
    function calls_regular_laravel_exception_handler()
    {
        $this->tapLaravelVersionTill(5.4, function () {
            $this->assertTrue(true);
        });

        $this->tapLaravelVersionFrom(5.5, function () {
            $mock = Mockery::spy(DummyException::class);

            Route::get('route-ex', function () use ($mock) {
                throw $mock;
            });

            $tracker = $this->app->make(ExceptionTracker::class);

            $this->get('/route-ex');

            $mock->shouldHaveReceived('report')->once();
        });
    }
}
