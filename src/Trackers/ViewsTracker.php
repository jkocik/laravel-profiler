<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\View\View;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\LaravelListeners\ViewsListener;

class ViewsTracker extends BaseTracker
{
    /**
     * @var ViewsListener
     */
    protected $viewsListener;

    /**
     * ViewsTracker constructor.
     * @param Application $app
     * @param ViewsListener $viewsListener
     */
    public function __construct(Application $app, ViewsListener $viewsListener)
    {
        parent::__construct($app);

        $this->viewsListener = $viewsListener;
        $this->viewsListener->listen();
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $views = $this->viewsListener->views()->map(function (View $view) {
            return [
                'name' => $view->name(),
                'path' => $view->getPath(),
                'data' => $view->getData(),
            ];
        })->values();

        $this->data->put('views', $views);
    }
}
