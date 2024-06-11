#!/usr/bin/env bash

# Export Docker image Tag
BRANCH=$(git rev-parse --abbrev-ref HEAD)
replace='/'
replacewith='-'
BRANCH="${BRANCH/${replace}/${replacewith}}"
BRANCH="${BRANCH/${replace}/${replacewith}}"
export BRANCH

# Shut down currently running containers
composer stop

# Build new containers
docker compose -f docker-compose-tests.yml build

# Start fresh container instances
docker compose -f docker-compose-tests.yml up -d

# Database Migration
docker exec app php artisan db:wait
docker exec app php artisan migrate --seed

# Run phpunit inside Docker 'app' container
docker exec app ./vendor/bin/phpunit
