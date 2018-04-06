<?php

namespace JKocik\Laravel\Profiler\Trackers;

use JKocik\Laravel\Profiler\Services\GeneratorService;

class ApplicationTracker extends BaseTracker
{
    /**
     * @var GeneratorService
     */
    protected $generatorService;

    /**
     * ApplicationTracker constructor.
     * @param GeneratorService $generatorService
     */
    public function __construct(GeneratorService $generatorService)
    {
        parent::__construct();

        $this->generatorService = $generatorService;
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $this->meta->put('id', $this->generatorService->unique32CharsId());
    }
}
