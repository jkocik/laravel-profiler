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
        return Collection::make([
            'status' => $this->response->getStatusCode(),
            'status_text' => $this->getStatusText(),
        ]);
    }

    /**
     * @return Collection
     */
    public function data(): Collection
    {
        return Collection::make([
            'headers' => $this->response->headers->all(),
        ]);
    }

    /**
     * @return string
     */
    protected function getStatusText(): string
    {
        return $this->response::$statusTexts[$this->response->getStatusCode()] ?? 'unknown status';
    }
}
