#!/usr/bin/env bash

# Declare the 'environment' ('prod', 'dev', 'dev-db', 'dev-node', 'tests')
ENV=${1-"dev"}
IMAGE=${2-'production-image'}

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

# Build new containers
if [ $ENV == 'prod' ]; then
    # Retrieve the version number
    VERSION="$(head -n 1 version.txt)"
	docker build -t ${IMAGE}:"${VERSION//}" .
else
    docker compose -f docker-compose-${ENV}.yml build
fi
