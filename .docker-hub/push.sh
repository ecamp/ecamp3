#!/bin/bash
set -e

echo "$DOCKER_HUB_PASSWORD" | docker login -u "$DOCKER_HUB_USERNAME" --password-stdin
docker-compose -f .docker-hub/docker-compose.yml build
docker-compose -f .docker-hub/docker-compose.yml push backend frontend
