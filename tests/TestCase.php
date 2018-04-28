<?php

namespace JKocik\Laravel\Profiler\Tests;

use Closure;
use phpmock\MockBuilder;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use phpmock\environment\MockEnvironment;
use phpmock\functions\FixedValueFunction;
use JKocik\Laravel\Profiler\ServiceProvider;
use PHPUnit\Framework\TestCase as BaseTestCase;
use JKocik\Laravel\Profiler\Tests\Support\Framework;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use JKocik\Laravel\Profiler\Tests\Support\PHPMockValues;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

class TestCase extends BaseTestCase
{
    use MakesHttpRequests;
    use MockeryPHPUnitIntegration;

    /**
     * @var Framework
     */
    protected static $framework;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * @var MockEnvironment
     */
    protected $phpMock;

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
        $this->enablePhpMock();

        $this->app = $this->app();
    }

    /**
     * @return void
     */
    protected function tearDown()
    {
        $this->disablePhpMock();
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

    /**
     * @return void
     */
    protected function turnOffProcessors(): void
    {
        $this->app->make('config')->set('profiler.processors', []);
    }

    /**
     * @return void
     */
    protected function enablePhpMock(): void
    {
        $time = new MockBuilder();
        $time->setNamespace('JKocik\Laravel\Profiler\Trackers');
        $time->setName('time');
        $time->setFunctionProvider(new FixedValueFunction(PHPMockValues::TIME));

        $this->phpMock = new MockEnvironment();
        $this->phpMock->addMock($time->build());
        $this->phpMock->enable();
    }

    /**
     * @return void
     */
    protected function disablePhpMock(): void
    {
        $this->phpMock->disable();
    }
}
