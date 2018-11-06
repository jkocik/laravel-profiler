<?php

namespace JKocik\Laravel\Profiler\Processors;

use GuzzleHttp\Exception\ConnectException;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Events\ProfilerServerConnectionFailed;
use JKocik\Laravel\Profiler\Events\ProfilerServerConnectionSuccessful;

class StatusCommandProcessor extends BroadcastingProcessor
{
    /**
     * @param DataTracker $dataTracker
     * @return void
     */
    public function process(DataTracker $dataTracker): void
    {
        try {
            $response = $this->broadcast(
                $dataTracker,
                $this->configService->serverHttpConnectionUrl() . '/status'
            );

            $body = json_decode($response->getBody());

            event(new ProfilerServerConnectionSuccessful($body->sockets, $body->clients));
        } catch (ConnectException $e) {
            event(new ProfilerServerConnectionFailed());
            throw $e;
        }
    }
}
