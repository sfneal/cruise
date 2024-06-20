#!/usr/bin/env bash

# Declare the 'environment' ('prod', 'dev', 'dev-db', 'dev-node', 'tests')
ENV=${1-"dev"}
IMAGE=${2-'production-image'}

BRANCH="default"

# Export Docker image Tag
inside_git_repo="$(git rev-parse --is-inside-work-tree 2>/dev/null)"
if [ "$inside_git_repo" ]; then
	BRANCH=$(git rev-parse --abbrev-ref HEAD)
fi

set -e -u

export BRANCH

# Build new containers
if [[ "$ENV" == 'prod' ]]; then

    # Extract the 'image' value for the 'app' service
    IMAGE=$(grep -A 10 "services:" docker-compose.yml | grep -A 1 "app:" | grep "image:" | awk '{print $2}')
	docker build -t "${IMAGE}" .
else
    docker compose -f docker-compose-${ENV}.yml build
fi
