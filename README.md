# OrangeHRM API Wrapper

[![Latest Version on Packagist](https://img.shields.io/packagist/v/grantholle/orangehrm-api.svg?style=flat-square)](https://packagist.org/packages/grantholle/orangehrm-api)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/grantholle/orangehrm-api/run-tests?label=tests)](https://github.com/grantholle/orangehrm-api/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/grantholle/orangehrm-api/Check%20&%20fix%20styling?label=code%20style)](https://github.com/grantholle/orangehrm-api/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/grantholle/orangehrm-api.svg?style=flat-square)](https://packagist.org/packages/grantholle/orangehrm-api)

---

This is a very light client for the OrangeHRM API. It does not support all the capabilities of the API currently and is a work in progress.

## Installation

You can install the package via composer:

```bash
composer require grantholle/orangehrm-api
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="GrantHolle\OrangeHrm\OrangeHrmServiceProvider" --tag="orangehrm-config"
```

This is the contents of the published config file:

```php
return [
    'base_url' => env('ORANGEHRM_BASE_URL'),
    'client_id' => env('ORANGEHRM_CLIENT_ID'),
    'client_secret' => env('ORANGEHRM_CLIENT_SECRET'),
];
```

You should add the `ORANGEHRM_BASE_URL`, `ORANGEHRM_CLIENT_ID`, and `ORANGEHRM_CLIENT_SECRET` keys to your `.env` file. You can learn about how to create the api credentials in the [documentation](https://api.orangehrm.com/). 

## Usage

There are a handful of methods implemented, including `addEmployee`, `getEmployee`, and `updateEmployee`.

```php
use GrantHolle\OrangeHrm\OrangeHrmFacade;

$employees = OrangeHrmFacade::getEmployees();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
