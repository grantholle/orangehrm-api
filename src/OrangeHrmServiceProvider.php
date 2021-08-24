<?php

namespace GrantHolle\OrangeHrm;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use GrantHolle\OrangeHrm\Commands\OrangeHrmCommand;

class OrangeHrmServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('orangehrm-api')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_orangehrm-api_table')
            ->hasCommand(OrangeHrmCommand::class);
    }
}
