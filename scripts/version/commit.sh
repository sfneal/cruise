#!/usr/bin/env bash

message=${1}

# Commit to git
git commit -m "${message}" ./version.txt ./docker-compose.yml ./docker-compose-dev.yml ./docker-compose-dev-db.yml ./docker-compose-dev-node.yml
