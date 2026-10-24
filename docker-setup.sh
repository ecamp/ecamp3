#!/bin/bash
set -euo pipefail

npm ci --verbose

exec "$@"
