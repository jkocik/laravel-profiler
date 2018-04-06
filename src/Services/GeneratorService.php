<?php

namespace JKocik\Laravel\Profiler\Services;

class GeneratorService
{
    /**
     * @return string
     */
    public function unique32CharsId(): string
    {
        return md5(uniqid('', true));
    }
}
