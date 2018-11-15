<?php

namespace JKocik\Laravel\Profiler;

use Exception;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Services\LogService;
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
     * @var LogService
     */
    protected $logService;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * LaravelDataProcessor constructor.
     * @param Application $app
     * @param LogService $logService
     * @param ConfigService $configService
     */
    public function __construct(
        Application $app,
        LogService $logService,
        ConfigService $configService
    ) {
        $this->app = $app;
        $this->logService = $logService;
        $this->configService = $configService;
    }

    /**
     * @param DataTracker $dataTracker
     * @return void
     */
    public function process(DataTracker $dataTracker): void
    {
        if ($this->shouldNotProcess($dataTracker)) {
            return;
        }

        $this->configService->processors()->each(function (string $processor) use ($dataTracker) {
            try {
                $this->make($processor)->process($dataTracker);
            } catch (Exception $e) {
                $this->logService->error($e);
            }
        });
    }

    /**
     * @param string $processor
     * @return Processor
     */
    protected function make(string $processor): Processor
    {
        return $this->app->make($processor);
    }

    /**
     * @param DataTracker $dataTracker
     * @return bool
     */
    protected function shouldNotProcess(DataTracker $dataTracker): bool
    {
        if (! $dataTracker->meta()->has('path')) {
            return false;
        }

        return $this->configService->pathsToTurnOffProcessors()->map(function ($path) use ($dataTracker) {
            return stripos($dataTracker->meta()->get('path'), $path) !== false;
        })->contains(true);
    }
}
