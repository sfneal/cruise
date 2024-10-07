# sfneal/cruise

[![Packagist PHP support](https://img.shields.io/packagist/php-v/sfneal/cruise)](https://packagist.org/packages/sfneal/cruise)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/sfneal/cruise.svg?style=flat-square)](https://packagist.org/packages/sfneal/cruise)
[![Test Suite](https://github.com/sfneal/cruise/actions/workflows/tests.yml/badge.svg)](https://github.com/sfneal/cruise/actions/workflows/tests.yml)
[![Static Analysis](https://github.com/sfneal/cruise/actions/workflows/static-analysis.yml/badge.svg)](https://github.com/sfneal/cruise/actions/workflows/static-analysis.yml)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sfneal/cruise/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sfneal/cruise/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/sfneal/cruise.svg?style=flat-square)](https://packagist.org/packages/sfneal/cruise)

Cruise is a Laravel Sail inspired CLI for managing Docker services

## Installation

You can install the package via composer:

```bash
composer require sfneal/cruise
```

Run the cruise _**install**_ command to publish cruise assets.

This will add Dockerfiles, docker compose configs & other docker assets to your application root.

```bash
php artsian cruise:install
```

Run the cruise _**uninstall**_ command to remove published cruise assets

```bash
php artsian cruise:uninstall
```

## Usage

Once the `php artsian cruise:install` command has been run you will have access to various command and utilities for running & testing your laravel application

### Artisan commands

| Syntax                     | Description                                                                             |
|----------------------------|-----------------------------------------------------------------------------------------|
| `php artisan bump`         | Bump your application to the next major, minor or patch version                         |
| `php artisan version`      | Display your application current version                                                |
| `php artisan migrate:prod` | Run database migrations only when your application env is 'production'                  |
| `php artisan db:wait`      | Application start up hook that allows you to wait for your database to become available |


### Composer commands

| Syntax                    | Description                                                                              |
|---------------------------|------------------------------------------------------------------------------------------|
| `composer build`          | Docker build your application (prod or dev)                                              |
| `composer start-dev`      | Start docker services for your development environment                                   |
| `composer start-dev-db`   | Start docker services for your development environment with a local database             |
| `composer start-dev-node` | Start docker services for your development environment with a node webpack hot re-loader |
| `composer start-test`     | Start docker services for testing your application (app, cache & db)                     |
| `composer stop`           | Stop docker services & delete containers, volumes & networks                             |

### Testing

``` bash
composer test
```

### Cookbook
#### Create a new Laravel application with Laravel Breeze & cruise installed
```bash

# Create new Laravel app
composer create-project laravel/laravel test-app
cd test-app

# Install front-end dependencies
yarn install

# Install breeze
composer require laravel/breeze --dev
php artisan breeze:install

# Remove sail - replaced by cruise
composer remove laravel/sail

# Install cruise
composer require sfneal/cruise
php artisan cruise:install


# Start application running in dev environment
composer start-dev

# Stop application
composer stop
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email stephen.neal14@gmail.com instead of using the issue tracker.

## Credits

- [Stephen Neal](https://github.com/sfneal)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## PHP Package Boilerplate

This package was generated using the [PHP Package Boilerplate](https://laravelpackageboilerplate.com).
