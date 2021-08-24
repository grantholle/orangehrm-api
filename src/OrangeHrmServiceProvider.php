<?php

namespace GrantHolle\OrangeHrm;

use Illuminate\Support\ServiceProvider;

class OrangeHrmServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/orangehrm.php', 'orangehrm');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/orangehrm.php' => config_path('orangehrm.php'),
            ], 'orangehrm-config');
        }

        $this->app->bind(OrangeHrm::class, function ($app) {
            return new OrangeHrm(
                config('orangehrm.base_url'),
                config('orangehrm.client_id'),
                config('orangehrm.client_secret')
            );
        });
    }
}
