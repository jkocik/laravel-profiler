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
            $this->hideSecretValues(
                $this->app->make('config')->all()
            )
        );
    }

    /**
     * @param array $config
     * @return array
     */
    protected function hideSecretValues(array $config): array
    {
        $keys = array_keys($config);

        return array_map(function ($value) use (&$keys) {
            $key = array_shift($keys);

            if (is_array($value)) {
                return $this->hideSecretValues($value);
            }

            if (is_string($value) && preg_match('/^(password|key|secret)$/i', $key)) {
                $value = str_repeat('*', strlen($value));
            }

            return $value;
        }, $config);
    }
}
