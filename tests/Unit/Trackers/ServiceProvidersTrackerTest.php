<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use Illuminate\Support\ServiceProvider;
use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\ServiceProvidersTracker;

class ServiceProvidersTrackerTest extends TestCase
{
    /** @test */
    function has_loaded_service_providers()
    {
        $provider = new class($this->app) extends ServiceProvider {
            public function register() {}
        };
        $this->app->register($provider);
        $tracker = $this->app->make(ServiceProvidersTracker::class);

        $tracker->terminate();
        $serviceProviders = $tracker->data()->get('service_providers');

        $this->assertNotNull($serviceProviders);
        $this->assertTrue($serviceProviders->contains(get_class($provider)));
        $this->assertCount(count($serviceProviders), $this->app->getLoadedProviders());
        $this->assertSame(0, $serviceProviders->keys()[0]);
    }
}
