#!/bin/bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
if [ -f "$SCRIPT_DIR/.env" ]; then
  set -a
  # shellcheck source=/dev/null
  source "$SCRIPT_DIR/.env"
  set +a
fi

send_slack_release_notification() {
  local stable_tag="$1"
  local before_sha="$2"

  if [ -z "${SLACK_WEBHOOK_URL:-}" ]; then
    echo "Warning: SLACK_WEBHOOK_URL not set; skipping Slack notification."
    return 0
  fi

  set -euo pipefail

  local repo remote_url repo_url sha actor
  remote_url="$(git remote get-url origin 2>/dev/null || true)"
  repo="$(printf '%s' "$remote_url" | sed -E 's#.*github.com[:/]([^/]+\/[^/.]+)(\.git)?$#\1#')"
  repo_url="https://github.com/${repo}"
  sha="$(git rev-parse HEAD)"
  actor="$(git log -1 --pretty=format:'%an' 2>/dev/null || git config user.name || whoami)"

  local max_commits=8 max_subject_len=80 max_commits_text=2900
  local status="Success" emoji="✅"
  local title="Eyeon Portal Plugin - Release - ${status}"
  local notification_text="${emoji} ${title} 👤 ${actor}"
  local compare_url release_url git_log_args

  if [ -z "$before_sha" ] || [ "$before_sha" = "0000000000000000000000000000000000000000" ]; then
    compare_url="${repo_url}/commit/${sha}"
    git_log_args=(-n 10)
  else
    compare_url="${repo_url}/compare/${before_sha}...${sha}"
    git_log_args=("${before_sha}..${sha}")
  fi

  release_url="${repo_url}/releases/tag/${stable_tag}"

  slack_escape_mrkdwn() {
    local s="$1"
    s="${s//&/&amp;}"
    s="${s//</&lt;}"
    s="${s//>/&gt;}"
    printf '%s' "$s"
  }

  truncate_subject() {
    local s="$1"
    if [ "${#s}" -gt "$max_subject_len" ]; then
      printf '%s…' "${s:0:$((max_subject_len - 1))}"
    else
      printf '%s' "$s"
    fi
  }

  local commits_mrkdwn="" commit_count=0

  append_commit_line() {
    local full_hash="$1" short_hash="$2" subject="$3" author="$4"
    [ -z "$full_hash" ] && return 0
    [ "$commit_count" -ge "$max_commits" ] && return 1
    subject="$(truncate_subject "$(slack_escape_mrkdwn "$subject")")"
    author="$(slack_escape_mrkdwn "$author")"
    commits_mrkdwn="${commits_mrkdwn}• <${repo_url}/commit/${full_hash}|${short_hash}> - ${subject} (${author})"$'\n'
    commit_count=$((commit_count + 1))
  }

  append_git_commits() {
    local extra_args=()
    if [ "$1" != "-n" ] && [ "$1" != "-1" ]; then
      extra_args=(-n "$max_commits")
    fi
    while IFS=$'\x1f' read -r full_hash short_hash subject author; do
      append_commit_line "$full_hash" "$short_hash" "$subject" "$author" || break
    done < <(git log "$@" "${extra_args[@]}" --pretty=format:'%H%x1f%h%x1f%s%x1f%an' --no-decorate 2>/dev/null || true)
  }

  append_git_commits "${git_log_args[@]}"

  if [ -z "$commits_mrkdwn" ]; then
    commit_count=0
    append_git_commits -1 "$sha"
  fi

  if [ -z "$commits_mrkdwn" ]; then
    local short_sha="${sha:0:7}"
    commits_mrkdwn="• <${repo_url}/commit/${sha}|${short_sha}> - (manual or unavailable commit details)"$'\n'
  fi

  if [ "${#commits_mrkdwn}" -gt "$max_commits_text" ]; then
    local trimmed="" line next
    while IFS= read -r line; do
      [ -z "$line" ] && continue
      next="${trimmed}${line}"$'\n'
      if [ "${#next}" -gt "$max_commits_text" ]; then
        trimmed="${trimmed}_Commit list truncated. Use Compare Changes for full history._"$'\n'
        break
      fi
      trimmed="$next"
    done <<< "$commits_mrkdwn"
    commits_mrkdwn="$trimmed"
  fi

  local payload_file
  payload_file="$(mktemp)"
  jq -n \
    --arg text "$notification_text" \
    --arg title "$title" \
    --arg emoji "$emoji" \
    --arg author_name "$actor" \
    --arg compare "$compare_url" \
    --arg release "$release_url" \
    --arg commits "$commits_mrkdwn" \
    '{
      text: $text,
      blocks: [
        {
          type: "section",
          text: {
            type: "mrkdwn",
            text: ("*" + $emoji + " " + $title + "* 👤 *" + $author_name + "*")
          }
        },
        {
          type: "section",
          text: {
            type: "mrkdwn",
            text: $commits
          }
        },
        {
          type: "actions",
          elements: [
            {
              type: "button",
              text: {type: "plain_text", text: "Compare Changes"},
              url: $compare
            },
            {
              type: "button",
              text: {type: "plain_text", text: "View Release"},
              url: $release
            }
          ]
        }
      ]
    }' > "$payload_file"

  curl -fsS -H 'Content-Type: application/json' -d @"$payload_file" "$SLACK_WEBHOOK_URL"
  rm -f "$payload_file"
  echo "Slack notification sent."
}

# Read the stable tag from readme.txt
stable_tag=$(grep -E '^Stable tag:' readme.txt | awk '{print $NF}' | tr -d '\r')
before_sha=$(git rev-parse HEAD 2>/dev/null || echo "")

# Check if the stable tag is not empty
if [ -n "$stable_tag" ]; then
  # Accept commit message as a command line argument
  commit_message=$1

  # Add, commit, and push changes
  git add .
  git commit -m "$commit_message"
 
  # Push to master and check the exit status
  if git push origin master; then
    
    if git rev-parse -q --verify "refs/tags/$stable_tag" >/dev/null; then
      echo "Error: Tag '$stable_tag' already exists. Aborting script."
    else
      git tag $stable_tag
      git push origin $stable_tag

      # Release version
      gh release create $stable_tag --notes "$commit_message"

      echo "Version Released: $stable_tag"
      send_slack_release_notification "$stable_tag" "$before_sha" || echo "Warning: Slack notification failed."
    fi
  else
    echo "Error: Failed to push changes to master branch."
  fi
else
  echo "Error: Stable tag not found in readme.txt."
fi
