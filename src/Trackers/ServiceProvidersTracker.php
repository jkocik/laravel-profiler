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
        $this->data->put('serviceProviders', $this->loadedProviders());
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
}
