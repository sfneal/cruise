#!/usr/bin/env bash

# Base directory containing source code
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# Determine if the version bump is a major, minor or patch
while :; do
    case $1 in
        -ma|--major) type="major"
        ;;
        -mi|--minor) type="minor"
        ;;
        -p|--patch) type="patch"
        ;;
        *) break
    esac
    shift
done

# Retrieve the version number
VERSION="$(head -n 1 version.txt)"

# Get the new version number
# https://github.com/fsaintjacques/semver-tool
BUMP="$(${DIR}/semver bump ${type} ${VERSION})"

echo "${BUMP}"
