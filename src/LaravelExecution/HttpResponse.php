<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use JKocik\Laravel\Profiler\Contracts\ExecutionResponse;

class HttpResponse implements ExecutionResponse
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * HttpResponse constructor.
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Collection
     */
    public function meta(): Collection
    {
        return collect([
            'status' => $this->response->status(),
        ]);
    }

    /**
     * @return Collection
     */
    public function data(): Collection
    {
        return collect();
    }
}
