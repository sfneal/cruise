#!/usr/bin/env bash

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
