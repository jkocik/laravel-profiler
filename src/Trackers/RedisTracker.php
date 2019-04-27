<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\LaravelListeners\RedisListener;

class RedisTracker extends BaseTracker
{
    /**
     * @var RedisListener
     */
    protected $redisListener;

    /**
     * @var bool
     */
    protected $redisCanBeTracked = false;

    /**
     * RedisTracker constructor.
     * @param Application $app
     * @param RedisListener $redisListener
     */
    public function __construct(Application $app, RedisListener $redisListener)
    {
        parent::__construct($app);

        $this->enableRedisEvents();

        $this->redisListener = $redisListener;
        $this->redisListener->listen();
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $commands = $this->redisListener->commands()->map(function ($item) {
            return $this->terminateCommands($item);
        });

        $this->meta->put('redis_count', $this->redisListener->count());
        $this->meta->put('redis_can_be_tracked', $this->redisCanBeTracked);
        $this->data->put('redis', $commands);
    }

    /**
     * @param array $item
     * @return array
     */
    protected function terminateCommands(array $item): array
    {
        list($command, $time, $name, $parameters) = $item;

        return [
            'command' => $command,
            'time' => $time,
            'name' => $name,
            'parameters' => $parameters,
        ];
    }

    /**
     * @return void
     */
    protected function enableRedisEvents(): void
    {
        $manager = $this->app->make('redis');

        if (! method_exists($manager, 'enableEvents')) {
            return; // @codeCoverageIgnore
        }

        $this->redisCanBeTracked = true;
        $manager->enableEvents();
    }
}
