Setup code quality tool:
rm -rf .git/hooks
rm -rf vendor
Run: composer i
So sollte commit-msg File in hooks aussehen:
Run: cd .git/hooks && cat cat commit-msg
#!/bin/sh
#
## Run the hook command.
### Note: this will be replaced by the real command during copy.
#
GIT_USER=$(git config user.name)
GIT_EMAIL=$(git config user.email)
COMMIT_MSG_FILE=$1
## Fetch the GIT diff and format it as command input:
DIFF=$(git -c diff.mnemonicprefix=false -c diff.noprefix=false --no-pager diff -r -p -m -M --full-index --no-color --staged | cat)
### Grumphp env vars
export GRUMPHP_GIT_WORKING_DIR="$(git rev-parse --show-toplevel)"
### Run GrumPHP
(cd "./" && printf "%s\n" "${DIFF}" | exec 'vendor/bin/grumphp' 'git:commit-msg' "--git-user='$GIT_USER'" "--git-email='$GIT_EMAIL'" "$COMMIT_MSG_FILE")