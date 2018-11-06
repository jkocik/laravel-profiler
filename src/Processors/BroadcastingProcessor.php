<?php

namespace JKocik\Laravel\Profiler\Processors;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
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
        $this->broadcast(
            $dataTracker,
            $this->configService->serverHttpConnectionUrl()
        );
    }

    /**
     * @param DataTracker $dataTracker
     * @param string $url
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function broadcast(DataTracker $dataTracker, string $url): Response
    {
        return $this->client->request('POST', $url, [
            'json' => [
                'meta' => $dataTracker->meta()->toArray(),
                'data' => $dataTracker->data()->toArray(),
            ],
        ]);
    }
}
