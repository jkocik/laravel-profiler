<?php

namespace JKocik\Laravel\Profiler\Tests;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use JKocik\Laravel\Profiler\ServiceProvider;
use PHPUnit\Framework\TestCase as BaseTestCase;
use JKocik\Laravel\Profiler\Tests\Support\Framework;

class TestCase extends BaseTestCase
{
    /**
     * @var Framework
     */
    protected static $framework;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        static::$framework = new Framework();
    }

    /**
     * @return Application
     */
    public function appWithoutProfiler(): Application
    {
        $app = require __DIR__ . '/../frameworks/' . static::$framework->dir() . '/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

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
     * @param Closure $beforeServiceProvider
     * @return Application
     */
    protected function appWith(Closure $beforeServiceProvider): Application
    {
        $app = $this->appWithoutProfiler();

        $beforeServiceProvider($app);

        $app->register(ServiceProvider::class);

        return $app;
    }
}
