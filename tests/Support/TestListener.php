<?php

namespace JKocik\Laravel\Profiler\Tests\Support;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener as BaseTestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;

class TestListener implements BaseTestListener
{
    use TestListenerDefaultImplementation;

    /**
     * @var bool
     */
    protected $versionPrinted = false;

    /**
     * @param Test $test
     * @return void
     */
    public function startTest(Test $test): void
    {
        if (! $this->versionPrinted) {
            $this->printVersion($test);
        }
    }

    /**
     * @return void
     */
    protected function printVersion(Test $test): void
    {
        fwrite(STDERR, "APP: {$test->appBeforeBootstrap()->version()}\n");

        $this->versionPrinted = true;
    }
}
