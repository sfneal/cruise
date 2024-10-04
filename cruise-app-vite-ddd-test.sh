#!/usr/bin/env bash
set -e -u

CRUISE_BRANCH=$(git rev-parse --abbrev-ref HEAD)

cd ../

rm -rf test-app

composer create-project laravel/laravel test-app

cd test-app

yarn install

# composer remove laravel/sail
composer require laravel/breeze --dev

php artisan breeze:install blade --no-interaction

composer config minimum-stability dev
composer config repositories.0 '{"type": "vcs", "url": "https://github.com/sfneal/cruise"}'

composer require "sfneal/cruise dev-${CRUISE_BRANCH}"

php artisan cruise:install --ddd mydockerid myapplication vite

composer start-test
composer stop
rm -rf test-app
