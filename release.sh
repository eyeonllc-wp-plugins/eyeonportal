#!/bin/bash

# Read the stable tag from readme.txt
stable_tag=$(grep -E '^Stable tag:' readme.txt | awk '{print $NF}' | tr -d '\r')

# Check if the stable tag is not empty
if [ -n "$stable_tag" ]; then
  # Accept commit message as a command line argument
  commit_message=$1

  # Add, commit, and push changes
  git add .
  git commit -m "$commit_message"
 
  # Push to master and check the exit status
  if git push origin master; then
    # Tag and push the stable tag
    git tag $stable_tag
    
    # Push the tag and check the exit status
    if git push origin $stable_tag; then
      # Release version
      gh release create $stable_tag --notes "$commit_message"

      echo "Version Released: $stable_tag"
    else
      echo "Error: Failed to push tag to the repository."
    fi
  else
    echo "Error: Failed to push changes to master branch."
  fi
else
  echo "Error: Stable tag not found in readme.txt."
fi
