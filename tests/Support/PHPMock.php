<?php

namespace JKocik\Laravel\Profiler\Tests\Support;

use phpmock\MockBuilder;
use phpmock\environment\MockEnvironment;
use phpmock\functions\FixedValueFunction;

class PHPMock
{
    public const TIME = 1234567;
    public const PHP_VERSION = '7.1.2';
    public const MEMORY_USAGE = 1222333;

    /**
     * @return MockEnvironment
     */
    public static function phpMock(): MockEnvironment
    {
        $phpMock = new MockEnvironment();
        $phpMock->addMock(self::timeMock()->build());
        $phpMock->addMock(self::passthruMock()->build());
        $phpMock->addMock(self::phpVersionMock()->build());
        $phpMock->addMock(self::memoryUsageMock()->build());

        return $phpMock;
    }

    /**
     * @return MockBuilder
     */
    protected static function timeMock(): MockBuilder
    {
        return (new MockBuilder())
            ->setNamespace('JKocik\Laravel\Profiler\Trackers')
            ->setName('time')
            ->setFunctionProvider(new FixedValueFunction(self::TIME));
    }

    /**
     * @return MockBuilder
     */
    protected static function passthruMock(): MockBuilder
    {
        return (new MockBuilder())
            ->setNamespace('JKocik\Laravel\Profiler\Console')
            ->setName('passthru')
            ->setFunctionProvider(new FixedValueFunction(''));
    }

    /**
     * @return MockBuilder
     */
    protected static function phpVersionMock(): MockBuilder
    {
        return (new MockBuilder())
            ->setNamespace('JKocik\Laravel\Profiler\Trackers')
            ->setName('phpversion')
            ->setFunctionProvider(new FixedValueFunction(self::PHP_VERSION));
    }

    /**
     * @return MockBuilder
     */
    protected static function memoryUsageMock(): MockBuilder
    {
        return (new MockBuilder())
            ->setNamespace('JKocik\Laravel\Profiler\Services\Performance')
            ->setName('memory_get_peak_usage')
            ->setFunctionProvider(new FixedValueFunction(self::MEMORY_USAGE));
    }
}
