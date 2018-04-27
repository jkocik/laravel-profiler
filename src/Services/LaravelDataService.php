<?php

namespace JKocik\Laravel\Profiler\Services;

use Illuminate\Http\Request;
use JKocik\Laravel\Profiler\Http\NullRequest;
use JKocik\Laravel\Profiler\Http\NullResponse;
use Symfony\Component\HttpFoundation\Response;
use JKocik\Laravel\Profiler\Contracts\DataService;

class LaravelDataService implements DataService
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * LaravelDataService constructor.
     */
    public function __construct()
    {
        $this->setRequest(new NullRequest());
        $this->setResponse(new NullResponse());
    }

    /**
     * @param Request $request
     * @return void
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function request(): Request
    {
        return $this->request;
    }

    /**
     * @param Response $response
     * @return void
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function response(): Response
    {
        return $this->response;
    }
}
