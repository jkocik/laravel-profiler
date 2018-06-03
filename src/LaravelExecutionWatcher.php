<?php

namespace JKocik\Laravel\Profiler;

use JKocik\Laravel\Profiler\Contracts\ExecutionWatcher;
use JKocik\Laravel\Profiler\LaravelExecution\ViewsListener;
use JKocik\Laravel\Profiler\LaravelExecution\HttpRequestHandledListener;
use JKocik\Laravel\Profiler\LaravelExecution\ConsoleCommandFinishedListener;

class LaravelExecutionWatcher implements ExecutionWatcher
{
    /**
     * @var ViewsListener
     */
    protected $viewsListener;

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
     * @param ViewsListener $viewsListener
     * @param HttpRequestHandledListener $httpRequestHandledListener
     * @param ConsoleCommandFinishedListener $consoleCommandFinishedListener
     */
    public function __construct(
        ViewsListener $viewsListener,
        HttpRequestHandledListener $httpRequestHandledListener,
        ConsoleCommandFinishedListener $consoleCommandFinishedListener
    ) {
        $this->viewsListener = $viewsListener;
        $this->httpRequestHandledListener = $httpRequestHandledListener;
        $this->consoleCommandFinishedListener = $consoleCommandFinishedListener;
    }

    /**
     * @return void
     */
    public function watch(): void
    {
        $this->viewsListener->listen();
        $this->httpRequestHandledListener->listen();
        $this->consoleCommandFinishedListener->listen();
    }
}
