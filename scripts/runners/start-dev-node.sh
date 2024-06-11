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
docker compose -f docker-compose-node.yml build

# Start fresh container instances
docker compose -f docker-compose-node.yml up -d
