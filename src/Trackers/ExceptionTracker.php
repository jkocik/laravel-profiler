<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\LaravelExecution\ExceptionHandler;
use JKocik\Laravel\Profiler\LaravelListeners\ExceptionListener;

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

        $app->singleton(\App\Exceptions\Handler::class, ExceptionHandler::class);
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
                return Arr::except($trace, ['args']);
            }),
        ]);
    }
}
