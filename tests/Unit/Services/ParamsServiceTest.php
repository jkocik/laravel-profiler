<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Services;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Services\ParamsService;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyClassA;
use JKocik\Laravel\Profiler\Tests\Support\Fixtures\DummyClassB;

class ParamsServiceTest extends TestCase
{
    /** @test */
    function returns_value_of_primitive_param_type()
    {
        $paramsService = $this->app->make(ParamsService::class);

        $this->assertEquals(1, $paramsService->resolve(1));
        $this->assertEquals('something', $paramsService->resolve('something'));
        $this->assertEquals(['a', 'b'], $paramsService->resolve(['a', 'b']));
        $this->assertEquals(true, $paramsService->resolve(true));
        $this->assertEquals(false, $paramsService->resolve(false));
    }

    /** @test */
    function returns_array_of_object_param_if_is_available()
    {
        $paramsService = $this->app->make(ParamsService::class);
        $user = $this->user(['email' => 'a@example.com']);

        $this->assertEquals($user->toArray(), $paramsService->resolve($user));
    }

    /** @test */
    function returns_class_name_of_object_param_if_array_is_not_available()
    {
        $paramsService = $this->app->make(ParamsService::class);
        $dummyClassA = new DummyClassA();

        $this->assertEquals(DummyClassA::class, $paramsService->resolve($dummyClassA));
    }

    /** @test */
    function can_resolve_array_of_objects()
    {
        $paramsService = $this->app->make(ParamsService::class);
        $dummyClassA = new DummyClassA();
        $dummyClassB = new DummyClassB();

        $this->assertEquals([
            DummyClassA::class,
            DummyClassB::class,
        ], $paramsService->resolve([$dummyClassA, $dummyClassB]));
    }
}
