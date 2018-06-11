<?php

namespace JKocik\Laravel\Profiler\LaravelListeners;

use Illuminate\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use JKocik\Laravel\Profiler\Contracts\LaravelListener;

class ViewsListener implements LaravelListener
{
    /**
     * @var Collection
     */
    protected $views;

    /**
     * ViewsListener constructor.
     */
    public function __construct()
    {
        $this->views = new Collection();
    }

    /**
     * @return void
     */
    public function listen(): void
    {
        Event::listen('composing:*', function (...$view) {
            $this->views->push($this->resolveView($view));
        });
    }

    /**
     * @return Collection
     */
    public function views(): Collection
    {
        return $this->views;
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
