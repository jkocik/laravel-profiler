<?php

namespace JKocik\Laravel\Profiler\Services;

use Illuminate\Config\Repository;
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
        if ($this->app->environment($this->config->get('profiler.force_disable_on'))) {
            return false;
        }

        return $this->config->get('profiler.enabled') === true;
    }

    /**
     * @return array
     */
    public function trackers(): array
    {
        return $this->config->get('profiler.trackers');
    }

    /**
     * @return array
     */
    public function processors(): array
    {
        return $this->config->get('profiler.processors');
    }

    /**
     * @return string
     */
    public function broadcastingEvent(): string
    {
        return $this->config->get('profiler.broadcasting_event');
    }

    /**
     * @return string
     */
    public function broadcastingUrl(): string
    {
        $address = $this->config->get('profiler.broadcasting_address');
        $port = $this->config->get('profiler.broadcasting_port');

        return  $address . ':' . $port;
    }
}
