<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Support\Collection;

class PathsTracker extends BaseTracker
{
    /**
     * @return void
     */
    public function terminate(): void
    {
        $paths = $this->paths()->map(function ($path, $name) {
            return [
                'name' => $name,
                'path' => $path,
            ];
        })->values();

        $this->data->put('paths', $paths);
    }

    /**
     * @return Collection
     */
    protected function paths(): Collection
    {
        return Collection::make([
            'app_path' => $this->app->path(),
            'base_path' => $this->app->basePath(),
            'lang_path' => $this->app->langPath(),
            'config_path' => $this->app->configPath(),
            'public_path' => $this->app->publicPath(),
            'storage_path' => $this->app->storagePath(),
            'resource_path' => $this->resourcePath(),
            'database_path' => $this->app->databasePath(),
            'bootstrap_path' => $this->app->bootstrapPath(),
            'cached_config_path' => $this->app->getCachedConfigPath(),
            'cached_routes_path' => $this->app->getCachedRoutesPath(),
            'cached_services_path' => $this->app->getCachedServicesPath(),
            'cached_packages_path' => $this->getCachedPackagesPath(),
            'environment_file_path' => $this->app->environmentFilePath(),
        ])->filter(function ($item) {
            return !! $item;
        });
    }

    /**
     * @return string
     */
    protected function resourcePath(): string
    {
        return method_exists($this->app, 'resourcePath')
            ? $this->app->resourcePath()
            : '';
    }

    /**
     * @return string
     */
    protected function getCachedPackagesPath(): string
    {
        return method_exists($this->app, 'getCachedPackagesPath')
            ? $this->app->getCachedPackagesPath()
            : '';
    }
}
