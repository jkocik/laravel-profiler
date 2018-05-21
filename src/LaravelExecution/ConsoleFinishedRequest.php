<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputInterface;
use JKocik\Laravel\Profiler\Contracts\ExecutionRequest;

class ConsoleFinishedRequest implements ExecutionRequest
{
    /**
     * @var null|string
     */
    protected $command;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * ConsoleFinishedRequest constructor.
     * @param null|string $command
     * @param InputInterface $input
     */
    public function __construct(?string $command, InputInterface $input)
    {
        $this->command = $command;
        $this->input = $input;
    }

    /**
     * @return Collection
     */
    public function meta(): Collection
    {
        return Collection::make([
            'type' => 'command-finished',
            'path' => $this->command,
        ]);
    }

    /**
     * @return Collection
     */
    public function data(): Collection
    {
        return Collection::make([
            'arguments' => $this->input->getArguments(),
            'options' => $this->input->getOptions(),
        ]);
    }
}
