<?php

namespace JKocik\Laravel\Profiler\Processors;

use ElephantIO\Client;
use JKocik\Laravel\Profiler\ProfilerConfig;
use JKocik\Laravel\Profiler\Contracts\Processor;
use JKocik\Laravel\Profiler\Contracts\DataTracker;

class BroadcastingProcessor implements Processor
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * BroadcastingProcessor constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param DataTracker $dataTracker
     * @return void
     */
    public function process(DataTracker $dataTracker): void
    {
        $this->client->initialize();
        $this->client->emit(ProfilerConfig::broadcastingEvent(), [
            'meta' => $dataTracker->meta(),
            'data' => $dataTracker->data(),
        ]);
    }
}
