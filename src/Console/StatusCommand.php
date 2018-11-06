<?php

namespace JKocik\Laravel\Profiler\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use JKocik\Laravel\Profiler\Services\ConfigService;
use JKocik\Laravel\Profiler\Services\ConsoleService;
use JKocik\Laravel\Profiler\Processors\StatusCommandProcessor;
use JKocik\Laravel\Profiler\Events\ProfilerServerConnectionFailed;
use JKocik\Laravel\Profiler\Events\ProfilerServerConnectionSuccessful;

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
     * @var ConsoleService
     */
    protected $consoleService;

    /**
     * @var bool
     */
    protected $connectionStatusIsUnknown = true;

    /**
     * StatusCommand constructor.
     * @param ConfigService $configService
     * @param ConsoleService $consoleService
     */
    public function __construct(ConfigService $configService, ConsoleService $consoleService)
    {
        parent::__construct();

        $this->configService = $configService;
        $this->consoleService = $consoleService;
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $this->configService->overrideProcessors([
            StatusCommandProcessor::class,
        ]);

        $this->printProfilerStatus();

        if (! $this->configService->isProfilerEnabled()) {
            return;
        }

        $this->printTrackersStatus();

        $this->printConnectionStatus();
    }

    /**
     * @return void
     */
    protected function printProfilerStatus(): void
    {
        $this->line("1) {$this->consoleService->envInfo()}");
        $this->info($this->consoleService->profilerStatusInfo());
    }

    /**
     * @return void
     */
    protected function printTrackersStatus(): void
    {
        $this->line('');
        $this->line("2) {$this->consoleService->trackersStatusInfo()}");

        $this->configService->trackers()->each(function ($tracker) {
            $this->info("- {$tracker}");
        });

        $this->comment($this->consoleService->trackersCommentLine1());
        $this->comment($this->consoleService->trackersCommentLine2());
    }

    /**
     * @return void
     */
    protected function printConnectionStatus(): void
    {
        $this->line('');
        $this->line("3) {$this->consoleService->connectionStatusInfo()}");

        Event::listen(ProfilerServerConnectionSuccessful::class, function (ProfilerServerConnectionSuccessful $event) {
            $this->info($this->consoleService->connectionSuccessfulInfo());
            $this->info($this->consoleService->connectionSuccessfulSocketsInfo($event->socketsPort));
            $this->info($this->consoleService->connectionSuccessfulClientsInfo($event->countClients));

            $this->connectionStatusIsUnknown = false;
        });

        Event::listen(ProfilerServerConnectionFailed::class, function () {
            $this->error($this->error($this->consoleService->connectionFailedInfo()));

            $this->connectionStatusIsUnknown = false;
        });

        app()->terminating(function () {
            if (! $this->connectionStatusIsUnknown) {
                return;
            }

            $this->error($this->consoleService->connectionStatusUnknownInfo());
        });
    }
}
