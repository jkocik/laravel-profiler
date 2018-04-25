<?php

namespace JKocik\Laravel\Profiler\Trackers;

use Illuminate\Http\Response;

class ResponseTracker extends BaseTracker
{
    /**
     * @return void
     */
    public function terminate(): void
    {
        $response = $this->app->make(Response::class);

        $this->meta->put('status', $response->status());
    }
}
