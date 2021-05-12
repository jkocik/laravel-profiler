<?php

namespace JKocik\Laravel\Profiler\Tests;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Console\Kernel;
use phpmock\environment\MockEnvironment;
use JKocik\Laravel\Profiler\ServiceProvider;
use PHPUnit\Framework\TestCase as BaseTestCase;
use JKocik\Laravel\Profiler\Tests\Support\PHPMock;
use JKocik\Laravel\Profiler\Tests\Support\Framework;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;

class TestCase extends BaseTestCase
{
    use MakesHttpRequests;
    use InteractsWithSession;
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
    public static function setUpBeforeClass(): void
    {
        static::$framework = new Framework();
    }

    /**
     * @return Application
     */
    public function appBeforeBootstrap(): Application
    {
        return require __DIR__ . '/../frameworks/' . static::$framework->dir() . '/bootstrap/app.php';
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->enablePhpMock();

        $this->app = $this->app();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        $this->disablePhpMock();
    }

    /**
     * @return Application
     */
    protected function app(): Application
    {
        $app = $this->appBeforeBootstrap();

        $app->afterBootstrapping(RegisterProviders::class, function () use ($app) {
            $app->register(ServiceProvider::class);
        });

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * @param Closure $beforeServiceProvider
     * @return Application
     */
    protected function appWith(Closure $beforeServiceProvider): Application
    {
        $app = $this->appBeforeBootstrap();

        $app->afterBootstrapping(RegisterProviders::class, function () use ($app, $beforeServiceProvider) {
            $beforeServiceProvider($app);
            $app->register(ServiceProvider::class);
        });

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * @return string
     */
    protected function userClass(): string
    {
        return $this->app->make('config')->get('auth.providers.users.model');
    }

    /**
     * @return object
     */
    protected function factoryUser(): object
    {
        if (function_exists('factory')) {
            return factory($this->userClass());
        }

        return $this->userClass()::factory();
    }

    /**
     * @param array $attributes
     * @return Model
     */
    protected function user(array $attributes = []): Model
    {
        $userClass = $this->app->make('config')->get('auth.providers.users.model');

        return new $userClass($attributes);
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
        $this->phpMock = PHPMock::phpMock();
        $this->phpMock->enable();
    }

    /**
     * @return void
     */
    protected function disablePhpMock(): void
    {
        $this->phpMock->disable();
    }

    /**
     * @param float $version
     * @param Closure $callback
     * @return void
     */
    protected function tapLaravelVersionTill(float $version, Closure $callback): void
    {
        if (TESTS_FRAMEWORK_VERSION <= $version) {
            $callback->__invoke();
        }
    }

    /**
     * @param float $version
     * @param Closure $callback
     * @return void
     */
    protected function tapLaravelVersionFrom(float $version, Closure $callback): void
    {
        if (TESTS_FRAMEWORK_VERSION >= $version) {
            $callback->__invoke();
        }
    }

    /**
     * @param float $versionFrom
     * @param float $versionTill
     * @param Closure $callback
     * @return void
     */
    protected function tapLaravelVersionBetween(float $versionFrom, float $versionTill, Closure $callback): void
    {
        if (TESTS_FRAMEWORK_VERSION >= $versionFrom && TESTS_FRAMEWORK_VERSION <= $versionTill) {
            $callback->__invoke();
        }
    }
}
