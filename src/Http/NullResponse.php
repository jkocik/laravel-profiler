<?php

namespace JKocik\Laravel\Profiler\Http;

use Symfony\Component\HttpFoundation\Response;

class NullResponse extends Response
{
    /**
     * @return null
     */
    public function status()
    {
        return null;
    }
}
