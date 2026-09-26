#!/usr/bin/env bash
# Local test for the write-access predicate shared by the deployment gate workflows.
# Keep the predicate block identical to deployment-pr.yml, restore-backup-dev-pr.yml
# and test-write-access-check.yml.
set -eo pipefail

run_predicate() {
  # $1 = user, $2 = permission | unknown | malformed | api-error | empty
  local user="$1" permission="$2" result has_access
  case "$permission" in
    unknown)
      result='{"data":{"repository":{"collaborators":{"edges":[{"node":{"login":"BacLuc"},"permission":"WRITE"}]}}}}'
      ;;
    malformed)
      result='not json at all'
      ;;
    api-error)
      result='{"message":"Bad credentials"}'
      ;;
    empty)
      result='{"data":{"repository":{"collaborators":{"edges":[]}}}}'
      ;;
    *)
      result="{\"data\":{\"repository\":{\"collaborators\":{\"edges\":[{\"node\":{\"login\":\"$user\"},\"permission\":\"$permission\"}]}}}}"
      ;;
  esac
  has_access=$(echo "$result" | jq -r --arg user "$user" '.data.repository.collaborators.edges[]? | select(.node.login == $user) | .permission' | grep -E '^(WRITE|MAINTAIN|ADMIN)$' | head -1 || true)
  [ -n "$has_access" ]
}

failures=0
check() {
  local name="$1" user="$2" permission="$3" want="$4" got
  if run_predicate "$user" "$permission"; then got=allow; else got=deny; fi
  if [ "$got" = "$want" ]; then
    echo "ok   - $name ($got)"
  else
    echo "FAIL - $name (want $want, got $got)"
    failures=$((failures + 1))
  fi
}

check "WRITE allows"        testuser WRITE    allow
check "MAINTAIN allows"     testuser MAINTAIN allow
check "ADMIN allows"        testuser ADMIN    allow
check "READ denies"         testuser READ     deny
check "TRIAGE denies"       testuser TRIAGE   deny
check "NONE denies"         testuser NONE     deny
check "unknown user denies" testuser unknown  deny
check "malformed denies"    testuser malformed deny
check "api-error denies"    testuser api-error deny
check "empty edges denies"  testuser empty    deny

if [ "$failures" -gt 0 ]; then
  echo "$failures test(s) failed"
  exit 1
fi
echo "All predicate tests passed"
