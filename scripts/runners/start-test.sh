#!/usr/bin/env bash
set -e -u

# Export Docker image Tag
sh $(dirname "$0")/_get_branch.sh

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
