#!/usr/bin/env bash

BRANCH="default"

# Export Docker image Tag
inside_git_repo="$(git rev-parse --is-inside-work-tree 2>/dev/null)"

if [ "$inside_git_repo" ]; then
	BRANCH=$(git rev-parse --abbrev-ref HEAD)
fi

set -e -u

export BRANCH

# Shut down currently running containers
composer stop

# Build new containers
docker compose -f docker-compose-dev-node.yml build

# Start fresh container instances
docker compose -f docker-compose-dev-node.yml up -d
