#!/usr/bin/env bash
set -e -u

# Declare the 'environment' ('dev', 'dev-db', 'dev-node', 'tests')
ENV=${1-"dev"}

# Export Docker image Tag
sh $(dirname "$0")/_get_branch.sh

# Build new containers
docker compose -f docker-compose-${ENV}.yml build
