<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\Support\Facades\Event;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionListener;

class ConsoleCommandFinishedListener implements ExecutionListener
{
    /**
     * @var ExecutionData
     */
    protected $executionData;

    /**
     * ConsoleCommandFinishedListener constructor.
     * @param ExecutionData $executionData
     */
    public function __construct(ExecutionData $executionData)
    {
        $this->executionData = $executionData;
    }

    /**
     * @return void
     */
    public function listen(): void
    {
        Event::listen(\Illuminate\Console\Events\ArtisanStarting::class, function ($event) {
            $this->executionData->setRequest(new ConsoleStartingRequest());
            $this->executionData->setResponse(new ConsoleStartingResponse());
        });

        Event::listen(\Illuminate\Console\Events\CommandFinished::class, function ($event) {
            $this->executionData->setRequest(new ConsoleFinishedRequest($event->command, $event->input));
            $this->executionData->setResponse(new ConsoleFinishedResponse($event->exitCode));
        });
    }
}
