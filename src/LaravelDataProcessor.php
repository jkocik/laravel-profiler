<?php

namespace JKocik\Laravel\Profiler;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\Processor;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Services\ConfigService;
use JKocik\Laravel\Profiler\Contracts\DataProcessor;

class LaravelDataProcessor implements DataProcessor
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * LaravelDataProcessor constructor.
     * @param Application $app
     * @param ConfigService $configService
     */
    public function __construct(Application $app, ConfigService $configService)
    {
        $this->app = $app;
        $this->configService = $configService;
    }

    /**
     * @param DataTracker $dataTracker
     * @return void
     */
    public function process(DataTracker $dataTracker): void
    {
        $this->configService->processors()->each(function (string $processor) use ($dataTracker) {
            $this->makeProcessor($processor)->process($dataTracker);
        });
    }

    /**
     * @param string $processor
     * @return Processor
     */
    protected function makeProcessor(string $processor): Processor
    {
        return $this->app->make($processor);
    }
}
