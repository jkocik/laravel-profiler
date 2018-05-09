<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use JKocik\Laravel\Profiler\Contracts\ExecutionRequest;

class HttpRequest implements ExecutionRequest
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * HttpRequest constructor.
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
        return collect([
            'type' => $this->httpTypeWithSuffixes(),
            'method' => $this->request->method(),
            'path' => $this->request->path(),
        ]);
    }

    /**
     * @return Collection
     */
    public function data(): Collection
    {
        return collect();
    }

    /**
     * @return string
     */
    protected function httpTypeWithSuffixes(): string
    {
        return collect([
            'http' => true,
            'ajax' => !! $this->request->ajax(),
            'pjax' => !! $this->request->pjax(),
            'json' => !! $this->request->isJson(),
        ])->filter(function ($item) {
            return $item;
        })->keys()->implode(' / ');
    }
}
