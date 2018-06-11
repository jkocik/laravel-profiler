<?php

namespace JKocik\Laravel\Profiler;

use JKocik\Laravel\Profiler\Contracts\ExecutionWatcher;
use JKocik\Laravel\Profiler\LaravelListeners\HttpRequestHandledListener;
use JKocik\Laravel\Profiler\LaravelListeners\ConsoleCommandFinishedListener;

class LaravelExecutionWatcher implements ExecutionWatcher
{
    /**
     * @var HttpRequestHandledListener
     */
    protected $httpRequestHandledListener;

    /**
     * @var ConsoleCommandFinishedListener
     */
    protected $consoleCommandFinishedListener;

    /**
     * LaravelExecutionWatcher constructor.
     * @param HttpRequestHandledListener $httpRequestHandledListener
     * @param ConsoleCommandFinishedListener $consoleCommandFinishedListener
     */
    public function __construct(
        HttpRequestHandledListener $httpRequestHandledListener,
        ConsoleCommandFinishedListener $consoleCommandFinishedListener
    ) {
        $this->httpRequestHandledListener = $httpRequestHandledListener;
        $this->consoleCommandFinishedListener = $consoleCommandFinishedListener;
    }

    /**
     * @return void
     */
    public function watch(): void
    {
        $this->httpRequestHandledListener->listen();
        $this->consoleCommandFinishedListener->listen();
    }
}
