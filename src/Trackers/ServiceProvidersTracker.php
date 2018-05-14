<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Support\Collection;

class ServiceProvidersTracker extends BaseTracker
{
    /**
     * @return void
     */
    public function terminate(): void
    {
        $serviceProviders = $this->loadedProviders()->map(function ($provider) {
            return [
                'provider' => $provider,
                'registered' => $this->registered($provider),
                'booted' => $this->booted($provider),
            ];
        });

        $this->data->put('serviceProviders', $serviceProviders);
    }

    /**
     * @return Collection
     */
    protected function loadedProviders(): Collection
    {
        return Collection::make(
            array_keys($this->app->getLoadedProviders())
        );
    }

    /**
     * @param string $provider
     * @return bool
     */
    protected function registered(string $provider): bool
    {
        return in_array('register', get_class_methods($provider));
    }

    /**
     * @param string $provider
     * @return bool
     */
    protected function booted(string $provider): bool
    {
        return in_array('boot', get_class_methods($provider)) && $this->app->isBooted();
    }
}
