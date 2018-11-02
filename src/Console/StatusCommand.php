<?php

namespace JKocik\Laravel\Profiler\Console;

use Illuminate\Console\Command;
use JKocik\Laravel\Profiler\Services\ConfigService;

class StatusCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'profiler:status';

    /**
     * @var string
     */
    protected $description = 'Check Laravel Profiler status';

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * StatusCommand constructor.
     * @param ConfigService $configService
     */
    public function __construct(ConfigService $configService)
    {
        parent::__construct();

        $this->configService = $configService;
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        if (! $this->configService->isProfilerEnabled()) {
            $this->printDisabledStatus();
            return;
        }

        $this->printEnabledStatus();
    }

    /**
     * @return void
     */
    protected function printEnabledStatus(): void
    {
        $this->info('Laravel Profiler is enabled');
    }

    /**
     * @return void
     */
    protected function printDisabledStatus(): void
    {
        $this->info('Laravel Profiler is disabled');
    }
}
