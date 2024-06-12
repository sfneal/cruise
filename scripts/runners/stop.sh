#!/usr/bin/env bash
set -e -u

# Export Docker image Tag
BRANCH=$(git rev-parse --abbrev-ref HEAD)
replace='/'
replacewith='-'
BRANCH="${BRANCH/${replace}/${replacewith}}"
BRANCH="${BRANCH/${replace}/${replacewith}}"
export BRANCH

docker compose down -v --remove-orphans

docker compose -f docker-compose-dev-db.yml down -v --remove-orphans
