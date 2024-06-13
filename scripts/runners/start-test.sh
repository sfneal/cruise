#!/usr/bin/env bash

BRANCH="default"

# Export Docker image Tag
inside_git_repo="$(git rev-parse --is-inside-work-tree 2>/dev/null)"

if [ "$inside_git_repo" ]; then
	BRANCH=$(git rev-parse --abbrev-ref HEAD)
	replace='/'
	replacewith='-'
	BRANCH="${BRANCH/${replace}/${replacewith}}"
	BRANCH="${BRANCH/${replace}/${replacewith}}"
fi

set -e -u

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
