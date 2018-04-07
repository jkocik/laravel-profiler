<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Services\GeneratorService;

class ApplicationTracker extends BaseTracker
{
    /**
     * @var GeneratorService
     */
    protected $generatorService;

    /**
     * ApplicationTracker constructor.
     * @param Application $app
     * @param GeneratorService $generatorService
     */
    public function __construct(Application $app, GeneratorService $generatorService)
    {
        parent::__construct($app);

        $this->generatorService = $generatorService;
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $this->meta->put('id', $this->generatorService->unique32CharsId());
        $this->meta->put('version', $this->app->version());
        $this->meta->put('env', $this->app->environment());
    }
}
