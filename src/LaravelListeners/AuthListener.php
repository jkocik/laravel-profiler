<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use App\User;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
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
    public function user(): ?User
    {
        return $this->user ?? Auth::user();
    }
}
