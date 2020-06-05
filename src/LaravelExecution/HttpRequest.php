<?php

namespace JKocik\Laravel\Profiler\LaravelExecution;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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
        return Collection::make([
            'type' => 'http',
            'method' => $this->request->method(),
            'path' => $this->request->path(),
            'ajax' => $this->request->ajax(),
            'json' => $this->request->isJson(),
        ]);
    }

    /**
     * @return Collection
     */
    public function data(): Collection
    {
        return Collection::make([
            'pjax' => $this->request->pjax(),
            'url' => $this->request->url(),
            'query' => $this->request->query(),
            'ip' => $this->request->ip(),
            'header' => $this->request->header(),
            'input' => $this->request->input(),
            'files' => $this->files(),
            'cookie' => $this->request->cookie(),
        ]);
    }

    /**
     * @return Collection
     */
    protected function files(): Collection
    {
        $files = Collection::make($this->request->allFiles());

        return $this->filesMap($files);
    }

    /**
     * @param Collection $files
     * @return Collection
     */
    protected function filesMap(Collection $files): Collection
    {
        return $files->map(function ($file) {
            if (is_array($file)) {
                $files = Collection::make($file);
                return $this->filesMap($files);
            }

            return [get_class($file) => $this->file($file)];
        });
    }

    /**
     * @param UploadedFile $file
     * @return array
     */
    protected function file(UploadedFile $file): array
    {
        return [
            'client original name' => $file->getClientOriginalName(),
            'client original extension' => $file->getClientOriginalExtension(),
            'client mime type' => $file->getClientMimeType(),
            'client size' => $this->clientSize($file),
            'path' => $file->path(),
        ];
    }

    /**
     * @param UploadedFile $file
     * @return int
     */
    protected function clientSize(UploadedFile $file): int
    {
        return method_exists($file, 'getClientSize')
            ? $file->getClientSize()
            : $file->getSize();
    }
}
