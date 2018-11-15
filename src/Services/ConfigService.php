<?php

namespace JKocik\Laravel\Profiler\Services;

use Illuminate\Config\Repository;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;

class ConfigService
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * ConfigService constructor.
     * @param Application $app
     * @param Repository $config
     */
    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * @return bool
     */
    public function isProfilerEnabled(): bool
    {
        $enabledOverrides = Collection::make($this->config->get('profiler.enabled_overrides'));
        $envToDisable = $enabledOverrides->filter(function ($enabled) {
            return ! $enabled;
        })->keys();

        if ($this->app->environment($envToDisable->toArray())) {
            return false;
        }

        return $this->config->get('profiler.enabled') === true;
    }

    /**
     * @return Collection
     */
    public function trackers(): Collection
    {
        return Collection::make($this->config->get('profiler.trackers'));
    }

    /**
     * @return Collection
     */
    public function processors(): Collection
    {
        return Collection::make($this->config->get('profiler.processors'));
    }

    /**
     * @param array $processors
     */
    public function overrideProcessors(array $processors): void
    {
        $this->config->set('profiler.processors', $processors);
    }

    /**
     * @return Collection
     */
    public function pathsToTurnOffProcessors(): Collection
    {
        return Collection::make($this->config->get('profiler.turn_off_processors_for_paths'));
    }

    /**
     * @return string
     */
    public function serverHttpConnectionUrl(): string
    {
        $address = $this->config->get('profiler.server_http.address');
        $port = $this->config->get('profiler.server_http.port');

        return  $address . ':' . $port;
    }

    /**
     * @return string
     */
    public function serverHttpPort(): string
    {
        return $this->config->get('profiler.server_http.port');
    }

    /**
     * @return string
     */
    public function serverSocketsPort(): string
    {
        return $this->config->get('profiler.server_sockets.port');
    }

    /**
     * @return bool
     */
    public function isViewsDataEnabled(): bool
    {
        return $this->config->get('profiler.data.views');
    }

    /**
     * @return bool
     */
    public function isEventsDataEnabled(): bool
    {
        return $this->config->get('profiler.data.events');
    }

    /**
     * @return bool
     */
    public function isEventsGroupEnabled(): bool
    {
        return $this->config->get('profiler.group.events');
    }

    /**
     * @param int $level
     * @return bool
     */
    public function handleExceptions(int $level): bool
    {
        return $this->config->get('profiler.handle_exceptions') === $level;
    }
}
