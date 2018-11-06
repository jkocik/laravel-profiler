<?php

namespace JKocik\Laravel\Profiler\Events;

class ProfilerServerConnectionSuccessful
{
    /**
     * @var int
     */
    public $socketsPort;

    /**
     * @var int
     */
    public $countClients;

    /**
     * ProfilerServerConnectionSuccessful constructor.
     * @param int $socketsPort
     * @param int $countClients
     */
    public function __construct(int $socketsPort, int $countClients)
    {
        $this->socketsPort = $socketsPort;
        $this->countClients = $countClients;
    }
}
