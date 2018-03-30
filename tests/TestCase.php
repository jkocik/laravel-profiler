<?php

namespace JKocik\Laravel\Profiler\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use JKocik\Laravel\Profiler\ServiceProvider;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->app = $this->app();
    }

    /**
     * @return Application
     */
    protected function app(): Application
    {
        $app = $this->appWithoutProfiler();

        $app->register(ServiceProvider::class);

        return $app;
    }

    /**
     * @return Application
     */
    protected function appWithoutProfiler(): Application
    {
        $app = require __DIR__ . '/../frameworks/laravel-55/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
