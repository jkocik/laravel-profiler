<?php

namespace JKocik\Laravel\Profiler\Services;

use ElephantIO\Engine\SocketIO\Version2X;

class BroadcastingEngineService extends Version2X
{
    /**
     * BroadcastingEngineService constructor.
     * @param ConfigService $configService
     */
    public function __construct(ConfigService $configService)
    {
        parent::__construct($configService->broadcastingUrl());
    }
}
