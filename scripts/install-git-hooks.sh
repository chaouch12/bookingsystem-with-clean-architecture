#!/bin/sh

set -eu

REPO_ROOT="$(git rev-parse --show-toplevel)"

chmod +x "$REPO_ROOT/.githooks/pre-commit" "$REPO_ROOT/.githooks/commit-msg"
git config core.hooksPath .githooks

printf '%s\n' "Installed Git hooks from .githooks"
