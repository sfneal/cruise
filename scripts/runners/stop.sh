#!/usr/bin/env bash
set -e -u

# Export Docker image Tag
sh $(dirname "$0")/_get_branch.sh

docker compose down -v --remove-orphans

docker compose -f docker-compose-dev-db.yml down -v --remove-orphans
