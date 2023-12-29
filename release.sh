#!/bin/bash

# Read the stable tag from readme.txt
stable_tag=$(grep -E '^Stable tag:' readme.txt | awk '{print $NF}')
echo "'$stable_tag is released"
echo "git tag $stable_tag"

# Check if the stable tag is not empty
if [ -n "$stable_tag" ]; then
  # Accept commit message as a command line argument
  commit_message=$1

  # Add, commit, and push changes
  git add .
  git commit -m "$commit_message"
  git push origin master

  # Tag and push the stable tag
  git tag $stable_tag
  git push origin $stable_tag

  echo "Version Released: $stable_tag"
else
  echo "Error: Stable tag not found in readme.txt."
fi
