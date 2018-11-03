<?php

namespace JKocik\Laravel\Profiler\Console;

use Illuminate\Console\Command;
use JKocik\Laravel\Profiler\Services\ConsoleService;

class ServerCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'profiler:server';

    /**
     * @var string
     */
    protected $description = 'Start Profiler Server';

    /**
     * @var ConsoleService
     */
    protected $consoleService;

    /**
     * ServerCommand constructor.
     * @param ConsoleService $consoleService
     */
    public function __construct(ConsoleService $consoleService)
    {
        parent::__construct();

        $this->consoleService = $consoleService;
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        passthru($this->consoleService->profilerServerCmd());
    }
}
