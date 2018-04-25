<?php

namespace JKocik\Laravel\Profiler\Trackers;

class RequestTracker extends BaseTracker
{
    /**
     * @return void
     */
    public function terminate(): void
    {
        $request = $this->app->make('request');

        $this->meta->put('method', $request->method());
        $this->meta->put('path', $request->path());
        $this->meta->put('is_ajax', $request->ajax());
    }
}
