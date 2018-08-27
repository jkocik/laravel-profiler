<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\View\View;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Services\ConfigService;
use JKocik\Laravel\Profiler\LaravelListeners\ViewsListener;

class ViewsTracker extends BaseTracker
{
    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * @var ViewsListener
     */
    protected $viewsListener;

    /**
     * ViewsTracker constructor.
     * @param Application $app
     * @param ConfigService $configService
     * @param ViewsListener $viewsListener
     */
    public function __construct(Application $app, ConfigService $configService, ViewsListener $viewsListener)
    {
        parent::__construct($app);

        $this->configService = $configService;
        $this->viewsListener = $viewsListener;
        $this->viewsListener->listen();
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        $views = $this->viewsListener->views()->map(function (View $view) {
            if ($this->configService->isViewsDataEnabled()) {
                return [
                    'name' => $view->name(),
                    'path' => $view->getPath(),
                    'data' => $view->getData(),
                ];
            }

            return [
                'name' => $view->name(),
                'path' => $view->getPath(),
            ];
        })->values();

        $this->data->put('views', $views);
    }
}
