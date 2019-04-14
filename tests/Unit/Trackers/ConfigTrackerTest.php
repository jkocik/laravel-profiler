<?php

namespace JKocik\Laravel\Profiler\Tests\Unit\Trackers;

use JKocik\Laravel\Profiler\Tests\TestCase;
use JKocik\Laravel\Profiler\Trackers\ConfigTracker;

class ConfigTrackerTest extends TestCase
{
    /** @test */
    function has_config_data()
    {
        $tracker = $this->app->make(ConfigTracker::class);

        $tracker->terminate();
        $config = $tracker->data()->get('config');

        $this->assertNotNull($config);
        $this->assertEquals(collect($this->app->make('config')->all())->keys(), $config->keys());
    }

    /** @test */
    function has_hidden_secret_config_data()
    {
        config()->set('my-config.password', '1');
        config()->set('my-config.next-config-level.password', '12');
        config()->set('my-config.next-config-level.even-deeper.password', '123');
        config()->set('my-config.PASSWORD', '1234');
        config()->set('my-config.key', '12345');
        config()->set('my-config.secret', '123456');
        config()->set('my-config.value_password', '1234567');
        config()->set('my-config.some_password_value', '12345678');
        config()->set('my-config.password-value', '123456789');

        $tracker = $this->app->make(ConfigTracker::class);

        $tracker->terminate();
        $config = $tracker->data()->get('config')->toArray();

        $this->assertEquals('*', $config['my-config']['password']);
        $this->assertEquals('**', $config['my-config']['next-config-level']['password']);
        $this->assertEquals('***', $config['my-config']['next-config-level']['even-deeper']['password']);
        $this->assertEquals('****', $config['my-config']['PASSWORD']);
        $this->assertEquals('*****', $config['my-config']['key']);
        $this->assertEquals('******', $config['my-config']['secret']);
        $this->assertEquals('1234567', $config['my-config']['value_password']);
        $this->assertEquals('12345678', $config['my-config']['some_password_value']);
        $this->assertEquals('123456789', $config['my-config']['password-value']);
    }
}
