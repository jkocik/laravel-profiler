<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\LaravelListeners\AuthListener;

class AuthTracker extends BaseTracker
{
    /**
     * @var AuthListener
     */
    protected $authListener;

    /**
     * AuthTracker constructor.
     * @param Application $app
     * @param AuthListener $authListener
     */
    public function __construct(Application $app, AuthListener $authListener)
    {
        parent::__construct($app);

        $this->authListener = $authListener;
        $this->authListener->listen();
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $this->data->put('auth', $this->authListener->user());
    }
}
