<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\ConfigTracker;

class ConfigTrackerTest extends TestCase
{
    /** @test */
    function has_config_data()
    {
        $tracker = $this->app->make(ConfigTracker::class);

        $tracker->terminate();
        $config = $tracker->data()->get('config');

        $this->assertNotNull($config);
        $this->assertEquals($this->app->make('config')->all(), $config->all());
    }
}
