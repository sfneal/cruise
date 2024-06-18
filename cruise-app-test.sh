#!/usr/bin/env bash
set -e -u

CRUISE_BRANCH=$(git rev-parse --abbrev-ref HEAD)

cd ../

rm -rf laravel-boilerplate-master

wget https://github.com/sfneal/laravel-boilerplate/archive/master.zip
unzip master.zip
rm master.zip

cd laravel-boilerplate-master

composer config minimum-stability dev

composer config repositories.0 '{"type": "vcs", "url": "https://github.com/sfneal/cruise"}'

composer require "sfneal/cruise dev-${CRUISE_BRANCH}"

php artisan cruise:install

# composer start-test
