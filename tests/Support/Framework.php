<?php

namespace JKocik\Laravel\Profiler\Tests\Support;

class Framework
{
    protected const VERSION = TESTS_FRAMEWORK_VERSION;

    /**
     * @return string
     */
    public function version(): string
    {
        return static::VERSION;
    }

    /**
     * @return string
     */
    public function versionWithoutDot(): string
    {
        return preg_replace('/\./', '', $this->version());
    }

    /**
     * @return string
     */
    public function dir(): string
    {
        return "laravel-{$this->versionWithoutDot()}";
    }

    /**
     * @return string
     */
    public function composerPackage(): string
    {
        return "laravel/laravel:{$this->version()}.*";
    }
}
