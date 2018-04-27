<?php

namespace JKocik\Laravel\Profiler\Http;

use Illuminate\Http\Request;

class NullRequest extends Request
{
    /**
     * @return null
     */
    public function method() {
        return null;
    }

    /**
     * @return null
     */
    public function path() {
        return null;
    }
}
