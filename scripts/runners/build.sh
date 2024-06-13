#!/usr/bin/env bash
set -e -u

# Declare the 'environment' ('dev', 'dev-db', 'dev-node', 'tests')
ENV=${1-"dev"}

# Export Docker image Tag
inside_git_repo="$(git rev-parse --is-inside-work-tree 2>/dev/null)"

if [ "$inside_git_repo" ]; then
	BRANCH=$(git rev-parse --abbrev-ref HEAD)
	replace='/'
	replacewith='-'
	BRANCH="${BRANCH/${replace}/${replacewith}}"
	BRANCH="${BRANCH/${replace}/${replacewith}}"
else
	BRANCH="default"
fi

export BRANCH

# Build new containers
docker compose -f docker-compose-${ENV}.yml build
