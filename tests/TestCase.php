<?php

namespace LivewireUI\Modal\Tests;

use Livewire\LivewireServiceProvider;
use LivewireUI\Modal\LivewireModalServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            LivewireModalServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:z1qfUazFM1lzfPy5sFcm8oykb2pQeS0/wuX79GdL3zI=');
    }
}