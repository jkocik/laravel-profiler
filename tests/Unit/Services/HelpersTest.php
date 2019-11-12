<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Services;

use JKocik\Laravel\Profiler\Tests\TestCase;

class HelpersTest extends TestCase
{
    /** @test */
    function helpers_functions_can_not_brake_application_by_second_definition_in_global_namespace()
    {
        require __DIR__ . '/../../../src/Services/helpers.php';

        $this->assertTrue(true);
    }
}
