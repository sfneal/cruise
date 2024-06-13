#!/usr/bin/env bash

BRANCH="default"

# Export Docker image Tag
inside_git_repo="$(git rev-parse --is-inside-work-tree 2>/dev/null)"

if [ "$inside_git_repo" ]; then
	BRANCH=$(git rev-parse --abbrev-ref HEAD)
fi

set -e -u

export BRANCH

docker compose down -v --remove-orphans

docker compose -f docker-compose-dev-db.yml down -v --remove-orphans
