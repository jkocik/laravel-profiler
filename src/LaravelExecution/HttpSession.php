<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\Support\Collection;
use Illuminate\Session\SessionManager;
use JKocik\Laravel\Profiler\Contracts\ExecutionSession;

class HttpSession implements ExecutionSession
{
    /**
     * @var SessionManager
     */
    protected $session;

    /**
     * HttpSession constructor.
     * @param SessionManager $session
     */
    public function __construct(SessionManager $session)
    {
        $this->session = $session;
    }

    /**
     * @return Collection
     */
    public function meta(): Collection
    {
        return Collection::make();
    }

    /**
     * @return Collection
     */
    public function data(): Collection
    {
        return Collection::make($this->session->all());
    }
}
