<?php

namespace JKocik\Laravel\Profiler\Tests\Feature;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;

class LaravelExecutionTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->turnOffProcessors();
    }

    /** @test */
    function laravel_execution_data_is_singleton()
    {
        $executionDataA = $this->app->make(ExecutionData::class);
        $executionDataB = $this->app->make(ExecutionData::class);

        $this->assertSame($executionDataA, $executionDataB);
    }
}
