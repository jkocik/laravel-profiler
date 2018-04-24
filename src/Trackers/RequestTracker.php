<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;

class RequestTracker extends BaseTracker
{
    /**
     * @return void
     */
    public function terminate(): void
    {
        $request = $this->app->make('request');

        $this->meta->put('method', $request->method());
        $this->meta->put('is_ajax', $request->ajax());
    }
}
