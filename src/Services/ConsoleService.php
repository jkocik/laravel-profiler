<?php

namespace JKocik\Laravel\Profiler\Services;

use Illuminate\Foundation\Application;

class ConsoleService
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * ConsoleService constructor.
     * @param Application $app
     * @param ConfigService $configService
     */
    public function __construct(Application $app, ConfigService $configService)
    {
        $this->app = $app;
        $this->configService = $configService;
    }

    /**
     * @return string
     */
    public function envInfo(): string
    {
        return "Your current environment is: {$this->app->environment()}";
    }

    /**
     * @return string
     */
    public function profilerStatusInfo(): string
    {
        $status = $this->configService->isProfilerEnabled() ? 'enabled' : 'disabled';

        return "Laravel Profiler is: {$status}";
    }

    /**
     * @return string
     */
    public function trackersStatusInfo(): string
    {
        return "You have {$this->configService->trackers()->count()} tracker(s) turned on";
    }

    /**
     * @return string
     */
    public function trackersCommentLine1(): string
    {
        return 'There are 14 trackers available out of the box';
    }

    /**
     * @return string
     */
    public function trackersCommentLine2(): string
    {
        return 'turn them on and off in profiler.php configuration file';
    }

    /**
     * @return string
     */
    public function connectionStatusInfo(): string
    {
        return "Trying to connect to Profiler Server on {$this->configService->serverHttpConnectionUrl()} ...";
    }

    /**
     * @return string
     */
    public function connectionSuccessfulInfo(): string
    {
        return 'Connected successfully';
    }

    /**
     * @param int $socketsPort
     * @return string
     */
    public function connectionSuccessfulSocketsInfo(int $socketsPort): string
    {
        return "Profiler Server sockets listening on port: {$socketsPort}";
    }

    /**
     * @param int $countClients
     * @return string
     */
    public function connectionSuccessfulClientsInfo(int $countClients): string
    {
        return "You have {$countClients} Profiler Client(s) connected at the moment";
    }

    /**
     * @return string
     */
    public function connectionFailedInfo(): string
    {
        return 'Connection failed';
    }

    /**
     * @return string
     */
    public function connectionStatusUnknownInfo(): string
    {
        return 'BroadcastingProcessor did not report connection status, connection status is unknown';
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

    /**
     * @param bool $manual
     * @return string
     */
    public function profilerClientCmd(bool $manual): string
    {
        $options = $manual ? '' : ' -o -s';

        return "node_modules/.bin/http-server node_modules/laravel-profiler-client/dist/{$options}";
    }
}
