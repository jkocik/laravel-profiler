<?php

namespace JKocik\Laravel\Profiler\Tests\Unit;

use Mockery;
use Illuminate\Foundation\Application;
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
        $serviceProviders = $tracker->data()->get('serviceProviders');

        $this->assertNotNull($serviceProviders);
        $this->assertTrue($serviceProviders->contains('provider', get_class($provider)));
        $this->assertCount(count($serviceProviders), $this->app->getLoadedProviders());
    }

    /** @test */
    function service_provider_is_registered_if_register_method_is_present()
    {
        if ($this->laravelVersionLowerThan(5.3)) {
            return;
        }

        $providerA = new class($this->app) extends ServiceProvider {
        };
        $providerB = new class($this->app) extends ServiceProvider {
            public function register() {}
        };
        $this->app->register($providerA);
        $this->app->register($providerB);
        $tracker = $this->app->make(ServiceProvidersTracker::class);

        $tracker->terminate();
        $serviceProviders = $tracker->data()->get('serviceProviders');

        $this->assertFalse($serviceProviders->where('provider', get_class($providerA))->first()['registered']);
        $this->assertTrue($serviceProviders->where('provider', get_class($providerB))->first()['registered']);
    }

    /** @test */
    function in_old_laravel_versions_service_provider_is_registered_if_register_method_is_present()
    {
        if ($this->laravelVersionEqualOrGreaterThan(5.3)) {
            return;
        }

        $provider = new class($this->app) extends ServiceProvider {
            public function register() {}
        };
        $this->app->register($provider);
        $tracker = $this->app->make(ServiceProvidersTracker::class);

        $tracker->terminate();
        $serviceProviders = $tracker->data()->get('serviceProviders');

        $this->assertTrue($serviceProviders->where('provider', get_class($provider))->first()['registered']);
    }

    /** @test */
    function service_provider_is_booted_if_boot_method_is_present()
    {
        $providerA = new class($this->app) extends ServiceProvider {
            public function register() {}
        };
        $providerB = new class($this->app) extends ServiceProvider {
            public function register() {}
            public function boot() {}
        };
        $this->app->register($providerA);
        $this->app->register($providerB);
        $tracker = $this->app->make(ServiceProvidersTracker::class);

        $tracker->terminate();
        $serviceProviders = $tracker->data()->get('serviceProviders');

        $this->assertFalse($serviceProviders->where('provider', get_class($providerA))->first()['booted']);
        $this->assertTrue($serviceProviders->where('provider', get_class($providerB))->first()['booted']);
    }

    /** @test */
    function service_provider_is_not_booted_if_app_is_not_booted()
    {
        $provider = new class($this->app) extends ServiceProvider {
            public function register() {}
            public function boot() {}
        };
        $this->app->register($provider);

        $app = Mockery::mock(Application::class);
        $app->shouldReceive('isBooted')->andReturn(false);
        $app->shouldReceive('getLoadedProviders')->andReturn($this->app->getLoadedProviders());
        $this->app->instance(Application::class, $app);
        $tracker = $this->app->make(ServiceProvidersTracker::class);

        $tracker->terminate();
        $serviceProviders = $tracker->data()->get('serviceProviders');

        $this->assertFalse($serviceProviders->where('provider', get_class($provider))->first()['booted']);
    }
}
