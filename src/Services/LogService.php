<?php

namespace JKocik\Laravel\Profiler\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class LogService
{
    const HANDLE_EXCEPTIONS_LOG = 1;
    const HANDLE_EXCEPTIONS_THROW = 666;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * LogService constructor.
     * @param ConfigService $configService
     */
    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * @param Exception $e
     * @throws Exception
     * @return void
     */
    public function error(Exception $e): void
    {
        if ($this->configService->handleExceptions(self::HANDLE_EXCEPTIONS_THROW)) {
            throw $e;
        }

        if ($this->configService->handleExceptions(self::HANDLE_EXCEPTIONS_LOG)) {
            Log::error($e);
        }
    }
}
