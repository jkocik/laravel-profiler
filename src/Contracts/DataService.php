<?php

namespace JKocik\Laravel\Profiler\Contracts;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface DataService
{
    /**
     * @param Request $request
     * @return void
     */
    public function setRequest(Request $request): void;

    /**
     * @return Request
     */
    public function request(): Request;

    /**
     * @param Response $response
     * @return void
     */
    public function setResponse(Response $response): void;

    /**
     * @return Response
     */
    public function response(): Response;
}
