<?php

namespace JKocik\Laravel\Profiler\Processors;

use GuzzleHttp\Client;
use JKocik\Laravel\Profiler\Contracts\Processor;
use JKocik\Laravel\Profiler\Contracts\DataTracker;
use JKocik\Laravel\Profiler\Services\ConfigService;

class BroadcastingProcessor implements Processor
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * BroadcastingProcessor constructor.
     * @param Client $client
     * @param ConfigService $configService
     */
    public function __construct(Client $client, ConfigService $configService)
    {
        $this->client = $client;
        $this->configService = $configService;
    }

    /**
     * @param DataTracker $dataTracker
     * @return void
     */
    public function process(DataTracker $dataTracker): void
    {
        $this->client->request('POST', $this->configService->serverHttpConnectionUrl(), [
            'json' => [
                'meta' => $dataTracker->meta()->toArray(),
                'data' => $dataTracker->data()->toArray(),
            ],
        ]);
    }
}
