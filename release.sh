#!/bin/bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
if [ -f "$SCRIPT_DIR/.env" ]; then
  set -a
  # shellcheck source=/dev/null
  source "$SCRIPT_DIR/.env"
  set +a
fi

send_slack_release_notification() {
  local status="$1"
  local stable_tag="${2:-}"
  local before_sha="${3:-}"
  local error_message="${4:-}"

  if [ -z "${SLACK_WEBHOOK_URL:-}" ]; then
    echo "Warning: SLACK_WEBHOOK_URL not set; skipping Slack notification."
    return 0
  fi

  set -euo pipefail

  local repo remote_url repo_url sha actor
  remote_url="$(git remote get-url origin 2>/dev/null || true)"
  repo="$(printf '%s' "$remote_url" | sed -E 's#.*github.com[:/]([^/]+\/[^/.]+)(\.git)?$#\1#')"
  repo_url="https://github.com/${repo}"
  sha="$(git rev-parse HEAD 2>/dev/null || echo "")"
  actor="$(git log -1 --pretty=format:'%an' 2>/dev/null || git config user.name || whoami)"

  local max_commits=8 max_subject_len=80 max_commits_text=2900
  local status_label emoji title notification_text compare_url release_url git_log_args

  if [ "$status" = "success" ]; then
    status_label="Success"
    emoji="✅"
  else
    status_label="Failed"
    emoji="❌"
  fi

  title="EyeOn WP Plugin - Release - ${status_label}"
  local version_suffix=""
  if [ "$status" = "success" ] && [ -n "$stable_tag" ]; then
    version_suffix=" :hash: *${stable_tag}*"
    notification_text="${emoji} ${title} 👤 ${actor}${version_suffix}"
  else
    notification_text="${emoji} ${title} 👤 ${actor}"
  fi

  if [ -n "$sha" ]; then
    if [ -z "$before_sha" ] || [ "$before_sha" = "0000000000000000000000000000000000000000" ] || [ "$before_sha" = "$sha" ]; then
      compare_url="${repo_url}/commit/${sha}"
      git_log_args=(-n 10)
    else
      compare_url="${repo_url}/compare/${before_sha}...${sha}"
      git_log_args=("${before_sha}..${sha}")
    fi
  else
    compare_url="${repo_url}"
    git_log_args=(-n 10)
  fi

  if [ "$status" = "success" ] && [ -n "$stable_tag" ]; then
    release_url="${repo_url}/releases/tag/${stable_tag}"
  else
    release_url=""
  fi

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
    local git_log_cmd=(git log "$@")
    if [ "$1" != "-n" ] && [ "$1" != "-1" ]; then
      git_log_cmd+=(-n "$max_commits")
    fi
    git_log_cmd+=(--pretty=format:'%H%x1f%h%x1f%s%x1f%an' --no-decorate)
    while IFS=$'\x1f' read -r full_hash short_hash subject author; do
      append_commit_line "$full_hash" "$short_hash" "$subject" "$author" || break
    done < <("${git_log_cmd[@]}" 2>/dev/null || true)
  }

  if [ -n "$sha" ]; then
    append_git_commits "${git_log_args[@]}"

    if [ -z "$commits_mrkdwn" ]; then
      commit_count=0
      append_git_commits -1 "$sha"
    fi
  fi

  if [ -z "$commits_mrkdwn" ]; then
    if [ -n "$sha" ]; then
      local short_sha="${sha:0:7}"
      commits_mrkdwn="• <${repo_url}/commit/${sha}|${short_sha}> - (manual or unavailable commit details)"$'\n'
    else
      commits_mrkdwn="_No commit details available._"$'\n'
    fi
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

  error_message="$(slack_escape_mrkdwn "$error_message")"

  local payload_file
  payload_file="$(mktemp)"
  jq -n \
    --arg text "$notification_text" \
    --arg title "$title" \
    --arg emoji "$emoji" \
    --arg author_name "$actor" \
    --arg version_suffix "$version_suffix" \
    --arg compare "$compare_url" \
    --arg release "$release_url" \
    --arg commits "$commits_mrkdwn" \
    --arg error "$error_message" \
    '{
      text: $text,
      blocks: [
        {
          type: "section",
          text: {
            type: "mrkdwn",
            text: ("*" + $emoji + " " + $title + "* 👤 *" + $author_name + "*" + $version_suffix)
          }
        },
        (if $error != "" then {
          type: "section",
          text: {
            type: "mrkdwn",
            text: ("*Error:* " + $error)
          }
        } else empty end),
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
            (if $release != "" then {
              type: "button",
              text: {type: "plain_text", text: "View Release"},
              url: $release
            } else empty end)
          ]
        }
      ]
    }' > "$payload_file"

  if curl -fsS -H 'Content-Type: application/json' -d @"$payload_file" "$SLACK_WEBHOOK_URL"; then
    rm -f "$payload_file"
    echo "Slack notification sent."
  else
    rm -f "$payload_file"
    return 1
  fi
}

notify_failure() {
  send_slack_release_notification "failed" "${stable_tag:-}" "${before_sha:-}" "$1" || echo "Warning: Slack notification failed."
}

notify_success() {
  send_slack_release_notification "success" "$stable_tag" "$before_sha" "" || echo "Warning: Slack notification failed."
}

# Read the stable tag from readme.txt
stable_tag=$(grep -E '^Stable tag:' readme.txt | awk '{print $NF}' | tr -d '\r')
before_sha=$(git rev-parse HEAD 2>/dev/null || echo "")

if [ -z "$stable_tag" ]; then
  echo "Error: Stable tag not found in readme.txt."
  notify_failure "Stable tag not found in readme.txt."
  exit 1
fi

commit_message=$1

git add .
if ! git commit -m "$commit_message"; then
  echo "Error: Failed to commit changes."
  notify_failure "Failed to commit changes."
  exit 1
fi

if ! git push origin master; then
  echo "Error: Failed to push changes to master branch."
  notify_failure "Failed to push changes to master branch."
  exit 1
fi

if git rev-parse -q --verify "refs/tags/$stable_tag" >/dev/null; then
  echo "Error: Tag '$stable_tag' already exists. Aborting script."
  notify_failure "Tag '$stable_tag' already exists."
  exit 1
fi

if ! git tag "$stable_tag"; then
  echo "Error: Failed to create tag '$stable_tag'."
  notify_failure "Failed to create tag '$stable_tag'."
  exit 1
fi

if ! git push origin "$stable_tag"; then
  echo "Error: Failed to push tag '$stable_tag'."
  notify_failure "Failed to push tag '$stable_tag'."
  exit 1
fi

if ! gh release create "$stable_tag" --notes "$commit_message"; then
  echo "Error: Failed to create GitHub release for '$stable_tag'."
  notify_failure "Failed to create GitHub release for '$stable_tag'."
  exit 1
fi

echo "Version Released: $stable_tag"
notify_success
