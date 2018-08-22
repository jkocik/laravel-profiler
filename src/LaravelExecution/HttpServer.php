<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use JKocik\Laravel\Profiler\Contracts\ExecutionServer;

class HttpServer implements ExecutionServer
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * HttpServer constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
        return Collection::make(
            $this->request->server()
        );
    }
}
