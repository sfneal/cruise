#!/usr/bin/env bash
set -e -u

# Export Docker image Tag
sh $(dirname "$0")/_get_branch.sh

# Shut down currently running containers
composer stop

# Build new containers
docker compose -f docker-compose-node.yml build

# Start fresh container instances
docker compose -f docker-compose-node.yml up -d
