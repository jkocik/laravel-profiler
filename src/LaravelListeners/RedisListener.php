<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Redis\Events\CommandExecuted;
use JKocik\Laravel\Profiler\Events\ResetTrackers;
use JKocik\Laravel\Profiler\Contracts\LaravelListener;

class RedisListener implements LaravelListener
{
    /**
     * @var array
     */
    protected $commands = [];

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @return void
     */
    public function listen(): void
    {
        $this->listenCommands();
        $this->listenResetTrackers();
    }

    /**
     * @return Collection
     */
    public function commands(): Collection
    {
        return Collection::make($this->commands);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * @return void
     */
    protected function listenCommands(): void
    {
        Event::listen(CommandExecuted::class, function (CommandExecuted $event) {
            $this->count++;

            array_push($this->commands, [
                $event->command,
                $event->time,
                $event->connectionName,
                $event->parameters,
            ]);
        });
    }

    /**
     * @return void
     */
    protected function listenResetTrackers(): void
    {
        Event::listen(ResetTrackers::class, function () {
            $this->commands = [];
            $this->count = 0;
        });
    }
}
