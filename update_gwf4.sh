#!/bin/bash
cd "$(dirname "$0")"

CORE="$(dirname "$0")"

echo "git pull all repos"
find . -iname ".git" -type d -exec sh -c "cd $CORE/{}/.. && echo `pwd`... && git pull" \;
