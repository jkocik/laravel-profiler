<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Support\Collection;

class ConfigTracker extends BaseTracker
{
    /**
     * @return void
     */
    public function terminate(): void
    {
        $this->data->put('config', $this->config());
    }

    /**
     * @return Collection
     */
    protected function config(): Collection
    {
        return Collection::make(
            $this->app->make('config')->all()
        );
    }
}
