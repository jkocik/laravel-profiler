<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\View\View;
use Illuminate\Foundation\Application;
use JKocik\Laravel\Profiler\Services\ConfigService;
use JKocik\Laravel\Profiler\Services\ParamsService;
use JKocik\Laravel\Profiler\LaravelListeners\ViewsListener;

class ViewsTracker extends BaseTracker
{
    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * @var ParamsService
     */
    protected $paramsService;

    /**
     * @var ViewsListener
     */
    protected $viewsListener;

    /**
     * ViewsTracker constructor.
     * @param Application $app
     * @param ConfigService $configService
     * @param ParamsService $paramsService
     * @param ViewsListener $viewsListener
     */
    public function __construct(
        Application $app,
        ConfigService $configService,
        ParamsService $paramsService,
        ViewsListener $viewsListener
    ) {
        parent::__construct($app);

        $this->configService = $configService;
        $this->paramsService = $paramsService;
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
                'params' => $this->paramsService->resolveFlattenFromArray($view->getData()),
            ];
        })->values();

        $this->data->put('views', $views);
    }
}
