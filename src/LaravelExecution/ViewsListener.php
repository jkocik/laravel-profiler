<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\View\View;
use Illuminate\Support\Facades\Event;
use JKocik\Laravel\Profiler\Contracts\ExecutionData;
use JKocik\Laravel\Profiler\Contracts\ExecutionListener;

class ViewsListener implements ExecutionListener
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
        Event::listen('composing:*', function (...$view) {
            $this->executionData->pushView($this->resolveView($view));
        });
    }

    /**
     * @param array $view
     * @return View
     */
    protected function resolveView(array $view): View
    {
        return $view[1][0] ?? $view[0];
    }
}
