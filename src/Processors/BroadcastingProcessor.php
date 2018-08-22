<?php

namespace JKocik\Laravel\Profiler\Processors;

use Exception;
use GuzzleHttp\Client;
use JKocik\Laravel\Profiler\ProfilerConfig;
use JKocik\Laravel\Profiler\Services\LogService;
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
     * @var LogService
     */
    protected $logService;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * BroadcastingProcessor constructor.
     * @param Client $client
     * @param LogService $logService
     * @param ConfigService $configService
     */
    public function __construct(Client $client, LogService $logService, ConfigService $configService)
    {
        $this->client = $client;
        $this->logService = $logService;
        $this->configService = $configService;
    }

    /**
     * @param DataTracker $dataTracker
     * @return void
     */
    public function process(DataTracker $dataTracker): void
    {
        try {
            $this->client->request('POST', $this->configService->broadcastingUrl(), [
                'json' => [
                    'meta' => $dataTracker->meta()->toArray(),
                    'data' => $dataTracker->data()->toArray(),
                ],
            ]);
        } catch (Exception $e) {
            $this->logService->error($e, $this->configService->broadcastingLogErrorsEnabled());
        }
    }
}
