<?php

namespace JKocik\Laravel\Profiler\Services\Performance;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Contracts\Timer;

class TimerService implements Timer
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Collection
     */
    protected $time;

    /**
     * @var string
     */
    protected $customNamePrefix = 'custom-';

    /**
     * TimerService constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->time = Collection::make();
    }

    /**
     * @param string $name
     * @return void
     */
    public function start(string $name): void
    {
        $this->time->put($name, [
            'start' => $this->now(),
        ]);
    }

    /**
     * @param string $name
     * @return void
     */
    public function finish(string $name): void
    {
        $this->time->put($name, array_merge($this->getByName($name), [
            'finish' => $this->now(),
        ]));
    }

    /**
     * @param string $name
     * @return void
     * @throws TimerException
     */
    public function startCustom(string $name): void
    {
        $customName = $this->customNamePrefix . $name;

        $this->guardTimerAlreadyStarted($customName);

        $this->start($customName);
    }

    /**
     * @param string $name
     * @return void
     * @throws TimerException
     */
    public function finishCustom(string $name): void
    {
        $customName = $this->customNamePrefix . $name;

        $this->guardTimerAlreadyFinished($customName);

        $this->guardTimerNotStartedYet($customName);

        $this->finish($customName);
    }

    /**
     * @return void
     */
    public function startLaravel(): void
    {
        $this->time->put('laravel', [
            'start' => $this->laravelStartTimeOrNow(),
        ]);
    }

    /**
     * @return void
     */
    public function finishLaravel(): void
    {
        $this->finish('laravel');
    }

    /**
     * @param string $name
     * @return float
     */
    public function milliseconds(string $name): float
    {
        return $this->millisecondsOf(
            $this->getByName($name)
        );
    }

    /**
     * @param string $name
     * @return float
     */
    public function millisecondsCustom(string $name): float
    {
        return $this->milliseconds($this->customNamePrefix . $name);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->time->filter(function ($item) {
            return $this->isCompleted($item);
        })->map(function ($item) {
            return $this->millisecondsOf($item);
        })->toArray();
    }

    /**
     * @return float
     */
    protected function now(): float
    {
        return \microtime(true);
    }

    /**
     * @return float
     */
    protected function laravelStartTimeOrNow(): float
    {
        return defined('LARAVEL_START') && ! $this->app->environment('testing')
            ? LARAVEL_START
            : $this->now();
    }

    /**
     * @param string $name
     * @return array
     */
    protected function getByName(string $name): array
    {
        return $this->time->first(function ($a, $b) use ($name) {
            return $a === $name || $b === $name;
        }) ?? [];
    }

    /**
     * @param array $item
     * @return float
     */
    protected function millisecondsOf(array $item): float
    {
        if (! $this->isCompleted($item)) {
            return -1;
        }

        return ($item['finish'] - $item['start']) * 1000;
    }

    /**
     * @param array $item
     * @return bool
     */
    protected function isCompleted(array $item): bool
    {
        return isset($item['start']) && isset($item['finish']);
    }

    /**
     * @param string $name
     * @return void
     * @throws TimerException
     */
    protected function guardTimerAlreadyStarted(string $name): void
    {
        if ($this->time->has($name)) {
            throw new TimerException("Laravel Profiler custom time tracker for {$name} already exists and can not be started twice");
        }
    }

    /**
     * @param string $name
     * @return void
     * @throws TimerException
     */
    protected function guardTimerAlreadyFinished(string $name): void
    {
        if ($this->isCompleted($this->getByName($name))) {
            throw new TimerException("Laravel Profiler custom time tracker for {$name} already exists and can not be finished twice");
        }
    }

    /**
     * @param string $name
     * @return void
     * @throws TimerException
     */
    protected function guardTimerNotStartedYet(string $name): void
    {
        if (! $this->time->has($name)) {
            throw new TimerException("Laravel Profiler custom time tracker for {$name} is not started yet");
        }
    }
}
