<?php

namespace GrantHolle\OrangeHrm\Tests;

use GrantHolle\OrangeHrm\OrangeHrmServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            OrangeHrmServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('orangehrm', [
            'base_url' => env('ORANGEHRM_BASE_URL'),
            'client_id' => env('ORANGEHRM_CLIENT_ID'),
            'client_secret' => env('ORANGEHRM_CLIENT_SECRET'),
        ]);
    }
}
