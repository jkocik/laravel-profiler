<?php

namespace JKocik\Laravel\Profiler\Services;

class ConsoleService
{
    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * ConsoleService constructor.
     * @param ConfigService $configService
     */
    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * @return string
     */
    public function profilerServerCmd(): string
    {
        $http = $this->configService->serverHttpPort();
        $ws = $this->configService->serverSocketsPort();

        return "node node_modules/laravel-profiler-client/server/server.js http={$http} ws={$ws}";
    }
}
