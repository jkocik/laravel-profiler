<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\Support\Collection;
use JKocik\Laravel\Profiler\Contracts\ExecutionRequest;

class ConsoleFinishedRequest implements ExecutionRequest
{
    /**
     * @var string
     */
    protected $command;

    /**
     * ConsoleFinishedRequest constructor.
     * @param string $command
     */
    public function __construct(string $command)
    {
        $this->command = $command;
    }

    /**
     * @return Collection
     */
    public function meta(): Collection
    {
        return collect([
            'type' => 'command',
            'method' => null,
            'path' => $this->command,
        ]);
    }

    /**
     * @return Collection
     */
    public function data(): Collection
    {
        return collect();
    }
}
