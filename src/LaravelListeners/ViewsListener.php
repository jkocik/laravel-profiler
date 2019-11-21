<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use Illuminate\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use JKocik\Laravel\Profiler\Events\ResetTrackers;
use JKocik\Laravel\Profiler\Contracts\LaravelListener;

class ViewsListener implements LaravelListener
{
    /**
     * @var array
     */
    protected $views = [];

    /**
     * @return void
     */
    public function listen(): void
    {
        $this->listenViews();
        $this->listenResetTrackers();
    }

    /**
     * @return Collection
     */
    public function views(): Collection
    {
        return Collection::make($this->views);
    }

    /**
     * @return void
     */
    protected function listenViews(): void
    {
        Event::listen('composing:*', function (...$view) {
            array_push($this->views, $this->resolveView($view));
        });
    }

    /**
     * @return void
     */
    protected function listenResetTrackers(): void
    {
        Event::listen(ResetTrackers::class, function () {
            $this->views = [];
        });
    }

    /**
     * @param array $view
     * @return View
     */
    protected function resolveView(array $view): View
    {
        return $view[1][0] ?? $view[0];
    }
}
