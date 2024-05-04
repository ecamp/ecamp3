#!/bin/sh

# shellcheck disable=SC2034
# Load git diff once for slight performance improvements
git_changes=$(git diff --name-only)

execute_or_run() {
    folder="$1"
    container_name="$2"
    service="$3"
    cmd="$4"
    use_cmd_as_entrypoint="${5:-false}" # default to false if not provided

    if echo "$git_changes" | grep -qE "^$folder/"; then
        if docker inspect --format="{{.State.Running}}" "$container_name" 2>/dev/null | grep -q "true"; then
            docker compose exec -d "$service" $cmd
        else
            # Check if we should use the command as the entrypoint
            if $use_cmd_as_entrypoint; then
                docker compose run --rm -d --entrypoint="$cmd" "$service"
            else
                docker compose run -d "$service" $cmd
            fi
        fi
    fi
}


# Frontend
execute_or_run "frontend" "ecamp3-frontend" "frontend" "npm run lint" &

# API/PHP
execute_or_run "api" "ecamp3-api" "php" "composer cs-fix" &

# Print
execute_or_run "print" "ecamp3-print" "print" "npm run lint" &

# PDF
execute_or_run "pdf" "ecamp3-pdf" "pdf" "npm run lint" &

# E2E
execute_or_run "e2e" "ecamp3-e2e" "e2e" "npm run lint" true

# Wait for all parallel jobs to complete
wait

echo "eCamp v3 Git-Hook run finished"