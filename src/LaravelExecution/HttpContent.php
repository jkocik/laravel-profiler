<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use JKocik\Laravel\Profiler\Contracts\ExecutionContent;

class HttpContent implements ExecutionContent
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * HttpContent constructor.
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
        return Collection::make();
    }

    /**
     * @return Collection
     */
    public function data(): Collection
    {
        return Collection::make([
            'content' => $this->response->getContent(),
        ]);
    }
}
