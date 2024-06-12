#!/usr/bin/env bash
set -e -u

# Declare the 'environment' ('dev', 'dev-db', 'dev-node', 'tests')
ENV=${1-"dev"}

# Export Docker image Tag
BRANCH=$(git rev-parse --abbrev-ref HEAD)
replace='/'
replacewith='-'
BRANCH="${BRANCH/${replace}/${replacewith}}"
BRANCH="${BRANCH/${replace}/${replacewith}}"
export BRANCH

# Build new containers
docker compose -f docker-compose-${ENV}.yml build
