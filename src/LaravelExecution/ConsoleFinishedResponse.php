<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\Support\Collection;
use JKocik\Laravel\Profiler\Contracts\ExecutionResponse;

class ConsoleFinishedResponse implements ExecutionResponse
{
    /**
     * @var int
     */
    protected $exitCode;

    /**
     * ConsoleFinishedResponse constructor.
     * @param int $exitCode
     */
    public function __construct(int $exitCode)
    {
        $this->exitCode = $exitCode;
    }

    /**
     * @return Collection
     */
    public function meta(): Collection
    {
        return Collection::make([
            'status' => $this->exitCode,
        ]);
    }

    /**
     * @return Collection
     */
    public function data(): Collection
    {
        return Collection::make();
    }
}
