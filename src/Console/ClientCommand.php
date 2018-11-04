<?php

namespace JKocik\Laravel\Profiler\Console;

use Illuminate\Console\Command;
use JKocik\Laravel\Profiler\Services\ConsoleService;

class ClientCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'profiler:client {--m|manual}';

    /**
     * @var string
     */
    protected $description = 'Start Profiler Client';

    /**
     * @var ConsoleService
     */
    protected $consoleService;

    /**
     * ClientCommand constructor.
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
        $this->line('Starting Profiler Client ...');

        passthru($this->consoleService->profilerClientCmd($this->option('manual')));
    }
}
