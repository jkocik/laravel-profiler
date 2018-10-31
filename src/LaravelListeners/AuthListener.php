<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use JKocik\Laravel\Profiler\Contracts\LaravelListener;

class AuthListener implements LaravelListener
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @return void
     */
    public function listen(): void
    {
        Event::listen(Logout::class, function (Logout $logout) {
            $this->user = $logout->user;
        });
    }

    /**
     * @return User|null
     */
    public function user(): ?Model
    {
        return $this->user ?? Auth::user();
    }
}
