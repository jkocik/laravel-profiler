<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\LaravelListeners\ExceptionListener;
use JKocik\Laravel\Profiler\LaravelExecution\ExceptionHandlerTillVersion6;
use JKocik\Laravel\Profiler\LaravelExecution\ExceptionHandlerFromVersion7;

class ExceptionTracker extends BaseTracker
{
    /**
     * @var ExceptionListener
     */
    protected $exceptionListener;

    /**
     * ExceptionTracker constructor.
     * @param Application $app
     * @param ExceptionListener $exceptionListener
     */
    public function __construct(Application $app, ExceptionListener $exceptionListener)
    {
        parent::__construct($app);

        $this->exceptionListener = $exceptionListener;
        $this->exceptionListener->listen();

        $this->bindExceptionHandler($app);
    }

    /**
     * @param Application $app
     * @return void
     */
    protected function bindExceptionHandler(Application $app): void
    {
        $version = (int) $app->version();

        $handler = $version < 7
            ? ExceptionHandlerTillVersion6::class
            : ExceptionHandlerFromVersion7::class; // @codeCoverageIgnore

        $app->singleton(\App\Exceptions\Handler::class, $handler);
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $exception = $this->exceptionListener->exception() ? $this->exception() : null;

        $this->data->put('exception', $exception);
    }

    /**
     * @return Collection
     */
    protected function exception(): Collection
    {
        $exception = $this->exceptionListener->exception();

        return Collection::make([
            'message' => $exception->getMessage(),
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => Collection::make($exception->getTrace())->map(function ($trace) {
                return Arr::except($trace, ['args', 'type']);
            }),
        ]);
    }
}
