<?php

namespace Ae3\LaravelLogsLayer\Tests;

use Ae3\LaravelLogsLayer\app\Providers\LogsLayerServiceProvider;
use \Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @param $app
     * @return string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            LogsLayerServiceProvider::class,
        ];
    }

    /**
     * @param $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }

}